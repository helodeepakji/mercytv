import os
import time
import datetime
import pandas as pd
import subprocess
import ntplib
from dateutil import parser

# Paths
EPG_FILE = "/var/www/html/catchup/epg/weekly_epg.xlsx"
RECORDINGS_PATH = "/var/www/html/catchup/recordings/channel1/"
LIVE_STREAM_URL = "https://5dd3981940faa.streamlock.net:443/mercytv/mercytv/playlist.m3u8"
LOG_FILE = "/var/www/html/catchup/logs/catchup.log"

# Ensure directories exist
os.makedirs(RECORDINGS_PATH, exist_ok=True)

def log_message(message):
    """Logs messages to file and prints them."""
    timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with open(LOG_FILE, "a") as log:
        log.write(f"[{timestamp}] {message}\n")
    print(message)

def get_current_ist_time():
    """Gets the current IST time from an NTP server."""
    try:
        client = ntplib.NTPClient()
        response = client.request('pool.ntp.org', version=3)
        utc_time = datetime.datetime.utcfromtimestamp(response.tx_time)
        ist_time = utc_time + datetime.timedelta(hours=5, minutes=30)
        return ist_time
    except Exception as e:
        log_message(f"NTP Time Sync Failed: {e}")
        return datetime.datetime.now() + datetime.timedelta(hours=5, minutes=30)

def delete_old_recordings():
    """Deletes recordings older than 24 hours."""
    now = time.time()
    for file in os.listdir(RECORDINGS_PATH):
        file_path = os.path.join(RECORDINGS_PATH, file)
        if os.path.isfile(file_path) and now - os.path.getmtime(file_path) > 86400:
            os.remove(file_path)
            log_message(f"Deleted old file: {file}")

def start_recording(duration, filename):
    """Starts recording unless a valid file already exists."""
    filepath = os.path.join(RECORDINGS_PATH, filename)
    
    # Check if file exists
    if os.path.exists(filepath):
        log_message(f"Skipping {filename}, already recorded.")
        return

    log_message(f"Recording {filename} for {duration} minutes...")
    
    command = [
        "ffmpeg", "-i", LIVE_STREAM_URL,
        "-c:v", "libx264", "-preset", "fast", "-b:v", "1000k",
        "-c:a", "aac", "-t", str(duration * 60),
        "-movflags", "faststart",
        "-f", "mp4", filepath
    ]
    
    try:
        result = subprocess.run(command, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        
        # Verify if file was actually created
        if os.path.exists(filepath):
            log_message(f"Recording completed: {filename}")
        else:
            log_message(f"Recording failed for {filename}: {result.stderr.decode()}")
    
    except Exception as e:
        log_message(f"Error recording {filename}: {e}")

def get_epg_for_day(target_date):
    """Fetches EPG schedule for the given date."""
    if not os.path.exists(EPG_FILE):
        log_message("No EPG file found. Using previous schedule.")
        return None
    
    target_date_str = target_date.strftime("%d-%m-%Y")
    
    try:
        xl = pd.ExcelFile(EPG_FILE)
        if target_date_str in xl.sheet_names:
            return xl.parse(target_date_str)
        
        for sheet_name in xl.sheet_names:
            df = xl.parse(sheet_name)
            if 'Date' in df.columns and 'Time' in df.columns:
                df['Date'] = pd.to_datetime(df['Date'], errors='coerce').dt.strftime("%d-%m-%Y")
                return df[df['Date'] == target_date_str]
    except Exception as e:
        log_message(f"Error reading EPG for {target_date_str}: {e}")
        return None

def schedule_recordings():
    """Schedules and manages all recordings properly."""
    delete_old_recordings()

    ist_now = get_current_ist_time()
    today_epg = get_epg_for_day(ist_now)
    
    if today_epg is None or today_epg.empty:
        log_message("No valid EPG found for today.")
        return

    current_program = None
    next_program = None

    for _, row in today_epg.iterrows():
        start_time_str = row['Time']
        duration = int(row['Duration'])
        program_name = row['Program Name']
        
        try:
            program_start_ist = parser.parse(start_time_str).replace(
                year=ist_now.year, month=ist_now.month, day=ist_now.day
            )
        except:
            log_message(f"Skipping invalid time format: {start_time_str}")
            continue

        program_end_time = program_start_ist + datetime.timedelta(minutes=duration)

        if program_start_ist <= ist_now < program_end_time:
            current_program = (program_start_ist, program_end_time, duration, program_name)
        elif program_start_ist > ist_now and next_program is None:
            next_program = program_start_ist  # Save the next program time

    if not current_program:
        if next_program:
            wait_time = (next_program - ist_now).total_seconds()
            log_message(f"No active program. Waiting {wait_time // 60} minutes until next program.")
            time.sleep(wait_time)
        else:
            log_message("No more programs today. Checking again in 10 minutes.")
            time.sleep(600)  # Wait 10 minutes and check again
        return

    program_start_ist, program_end_time, duration, program_name = current_program
    remaining_duration = (program_end_time - ist_now).seconds // 60

    timestamp = program_start_ist.strftime("%Y%m%d_%H%M")
    filename = f"channel1_{timestamp}.mp4"
    start_recording(remaining_duration, filename)

    # Wait until the program ends before scheduling the next one
    sleep_time = (program_end_time - get_current_ist_time()).total_seconds()
    if sleep_time > 0:
        log_message(f"Waiting {sleep_time // 60} minutes until next program.")
        time.sleep(sleep_time)

def run_script_in_background():
    """Runs the script as a background service."""
    log_message("Starting recording service in background...")
    while True:
        schedule_recordings()
        time.sleep(10)  # Check every 10 seconds

if __name__ == "__main__":
    run_script_in_background()
