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
    timestamp = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with open(LOG_FILE, "a") as log:
        log.write(f"[{timestamp}] {message}\n")
    print(message)

def get_current_ist_time():
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
    now = time.time()
    for file in os.listdir(RECORDINGS_PATH):
        file_path = os.path.join(RECORDINGS_PATH, file)
        if os.path.isfile(file_path):
            file_age = now - os.path.getmtime(file_path)  # Get file age in seconds
            if file_age > 86400:  # If older than 24 hours
                try:
                    os.remove(file_path)
                    log_message(f"Deleted old file: {file}")
                except Exception as e:
                    log_message(f"Error deleting {file}: {e}")

def start_recording(duration, filename):
    filepath = os.path.join(RECORDINGS_PATH, filename)
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
        subprocess.run(command, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        log_message(f"Recording completed: {filename}")
    except Exception as e:
        log_message(f"Error recording {filename}: {e}")

def get_epg_for_day(target_date):
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
    delete_old_recordings()
    ist_now = get_current_ist_time()
    today_epg = get_epg_for_day(ist_now)
    if today_epg is None or today_epg.empty:
        log_message("No valid EPG found for today.")
        return
    for _, row in today_epg.iterrows():
        start_time_str = row['Time']
        duration = int(row['Duration'])
        program_name = row['Program Name']
        try:
            program_start_ist = parser.parse(start_time_str).replace(year=ist_now.year, month=ist_now.month, day=ist_now.day)
        except:
            log_message(f"Skipping invalid time format: {start_time_str}")
            continue
        if ist_now > program_start_ist + datetime.timedelta(minutes=duration):
            continue
        elif ist_now > program_start_ist:
            remaining_duration = (program_start_ist + datetime.timedelta(minutes=duration) - ist_now).seconds // 60
            log_message(f"Recording partial program {program_name} for {remaining_duration} minutes.")
            duration = remaining_duration
        timestamp = program_start_ist.strftime("%Y%m%d_%H%M")
        filename = f"channel1_{timestamp}.mp4"
        start_recording(duration, filename)

def main():
    while True:
        schedule_recordings()
        time.sleep(60)

if __name__ == "__main__":
    main()
