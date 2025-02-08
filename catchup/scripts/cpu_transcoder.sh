#!/bin/bash

INPUT_STREAM="https://5dd3981940faa.streamlock.net:443/mercytv/mercytv/playlist.m3u8"  # Replace with your RTMP or HLS input URL
OUTPUT_DIR="/var/www/html/hls_output"    # Desired HLS output directory
SEGMENT_TIME=4                            # Segment length in seconds (reduce for lower latency)

mkdir -p $OUTPUT_DIR

# Optimized FFmpeg command with reduced CPU usage
ffmpeg -i "$INPUT_STREAM" -preset ultrafast -tune zerolatency -g 48 -sc_threshold 0 -keyint_min 48 -r 30 \
-filter_complex "[0:v]split=2[v1][v2]; 
[v1]scale=w=640:h=360[v360];
[v2]scale=w=1280:h=720[v720]" \
-map "[v360]" -c:v libx264 -b:v 500k -maxrate 550k -bufsize 800k -map a:0 -c:a aac -b:a 64k -f hls -hls_time $SEGMENT_TIME -hls_flags delete_segments "$OUTPUT_DIR/360p.m3u8" \
-map "[v720]" -c:v libx264 -b:v 1500k -maxrate 1300k -bufsize 2000k -map a:0 -c:a aac -b:a 96k -f hls -hls_time $SEGMENT_TIME -hls_flags delete_segments "$OUTPUT_DIR/720p.m3u8"
