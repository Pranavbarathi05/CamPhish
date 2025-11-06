#!/bin/bash
# Cleanup script for CamPhish
# Removes all unnecessary files and logs

echo "Starting cleanup of unnecessary files and logs..."

# Remove log files
echo "Removing log files..."
rm -f *.log
rm -f .cloudflared.log

# Remove any text files in the repo root (temporary markers, saved IPs, etc.)
echo "Removing text files..."
rm -f *.txt

# Remove temporary location files
echo "Removing temporary location files..."
rm -f location_*.txt
rm -f current_location.bak

# Remove captured images
echo "Removing captured images..."
rm -f cam*.png

# Remove temporary HTML files
echo "Removing temporary HTML files..."
rm -f index.php
rm -f index2.html
rm -f index3.html

# Clean saved locations directory but keep the directory itself
echo "Cleaning saved locations directory..."
if [ -d "saved_locations" ]; then
    rm -f saved_locations/*
fi

# Remove any other temporary files
echo "Removing other temporary files..."
rm -f LocationLog.log
rm -f LocationError.log
rm -f Log.log

# Also remove any remaining .log or .txt files (catch-all)
rm -f *.log
rm -f *.txt

# Truncate (clear) specific cloudfare files if present, or create empty ones
echo "Truncating cloudfare files (cloudfare.log, cloudfare)..."
:> cloudfare.log
:> cloudfare

# Remove cloudflared binary and related files if present
echo "Removing cloudflared binaries and archives..."
rm -f cloudflared
rm -f cloudflared.exe
rm -f cloudflared.tgz
rm -f cloudflared-linux-amd64
rm -f cloudflared-linux-arm64
rm -f cloudflared-windows-amd64.exe

echo "Cleanup completed successfully!" 