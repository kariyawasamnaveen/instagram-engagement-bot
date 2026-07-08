#!/bin/bash
# Finds and kills chrome/chromium processes orphaned to PPID=1
pids=$(ps -eo pid,ppid,comm | grep -E 'chrome|chromium' | awk '$2=="1" {print $1}')
if [ -n "$pids" ]; then
    echo "$(date): Killing orphaned Chrome processes: $pids" >> /var/log/clean_orphans.log
    echo $pids | xargs kill -9
fi
