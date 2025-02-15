import os
import time
import datetime
import requests
import subprocess
import pytz

# API Endpoint
CURRENT_PROGRAM_API = "http://103.89.46.115/api/programApi.php?type=currentProgram"

# Paths
RECORDINGS_PATH = "/var/www/html/catchup/recordings/channel1/"
LIVE_STREAM_URL = "https://5dd3981940faa.streamlock.net:443/mercytv/mercytv/playlist.m3u8"
LOG_FILE = "/var/www/html/catchup/logs/catchup.log"

# Timezone setup
IST = pytz.timezone("Asia/Kolkata")

# Ensure directories exist
os.makedirs(RECORDINGS_PATH, exist_ok=True)

def log_message(message):
    """Logs messages to a file and prints them"""
    timestamp = datetime.datetime.now(IST).strftime("%Y-%m-%d %H:%M:%S")
    with open(LOG_FILE, "a") as log:
        log.write(f"[{timestamp}] {message}\n")
    print(f"[{timestamp}] {message}")

def delete_old_recordings():
    """Deletes recordings older than 24 hours"""
    now = time.time()
    for file in os.listdir(RECORDINGS_PATH):
        file_path = os.path.join(RECORDINGS_PATH, file)
        if os.path.isfile(file_path) and now - os.path.getmtime(file_path) > 86400:  # 24 hours
            os.remove(file_path)
            log_message(f"Deleted old file: {file}")

def fetch_current_program():
    """Fetches the currently airing program from API and uses IST for all timestamps"""
    try:
        response = requests.get(CURRENT_PROGRAM_API)
        data = response.json()

        if data and "id" in data and "program" in data and "duration" in data and "time" in data:
            program_id = data["id"]
            program_name = data["program"]
            duration = int(data["duration"])

            # Parse the time from the API (in IST)
            ist_time = datetime.datetime.strptime(data["time"], "%H:%M:%S").time()
            ist_datetime = datetime.datetime.combine(datetime.datetime.now(IST).date(), ist_time)
            ist_datetime = IST.localize(ist_datetime)  # Localize to IST

            # Log for debugging
            log_message(f"Program: {program_name}, Start Time (IST): {ist_datetime}, Duration: {duration} minutes")

            return {"id": program_id, "program_name": program_name, "duration": duration, "start_time": ist_datetime}
        else:
            log_message("Invalid response from API")
            return None
    except Exception as e:
        log_message(f"Error fetching current program: {e}")
        return None

def start_recording(duration, filename):
    """Starts recording the live stream for the exact duration"""
    filepath = os.path.join(RECORDINGS_PATH, filename)

    if os.path.exists(filepath):
        log_message(f"Skipping {filename}, already recorded.")
        return
    
    log_message(f"Recording {filename} for {duration} minutes...")
    command = [
        "ffmpeg", "-i", LIVE_STREAM_URL,
        "-c:v", "libx264", "-preset", "fast", "-b:v", "1000k",
        "-c:a", "aac", "-b:a", "128k",
        "-t", str(duration * 60),
        "-movflags", "faststart",
        "-f", "mp4", filepath
    ]

    try:
        subprocess.run(command, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        log_message(f"Recording completed: {filename}")
    except Exception as e:
        log_message(f"Error recording {filename}: {e}")

def run_recording_loop():
    """Main loop that fetches the current program and records"""
    while True:
        delete_old_recordings()

        current_program = fetch_current_program()
        if not current_program:
            log_message("No valid current program found. Retrying in 1 minute...")
            time.sleep(60)
            continue

        program_id = current_program['id']
        program_name = current_program['program_name']
        duration = current_program['duration']
        start_time = current_program['start_time']
        now = datetime.datetime.now(IST)  # Ensure 'now' is in IST

        # Calculate elapsed and remaining time
        elapsed_time = (now - start_time).total_seconds() // 60
        remaining_duration = max(0, duration - elapsed_time)

        # Debugging logs
        log_message(f"Current Time (IST, now): {now}")
        log_message(f"Elapsed Time: {elapsed_time} minutes, Remaining Duration: {remaining_duration} minutes")

        if remaining_duration <= 0:
            log_message(f"Skipping {program_name}, already ended.")
            time.sleep(60)
            continue

        # Generate filename in the format: id_channel1_timestamp.mp4
        timestamp = start_time.strftime("%Y%m%d_%H%M")
        filename = f"{program_id}_channel1_{timestamp}.mp4"

        # Start recording the remaining duration
        start_recording(int(remaining_duration), filename)

        # Immediately check for the next program after recording is done
        log_message("Recording completed. Checking for the next program...")

if __name__ == "__main__":
    run_recording_loop()
