import os

filepath = '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/remote_worker_updated.py'
with open(filepath, 'r') as f:
    content = f.read()

old_sleep = """            if local_time.hour >= 23 or local_time.hour < 7:
                debug_log(f"Account {acc['username']} is sleeping ({local_time.hour}:00 in {tz})")
                return False, "SLEEP_MODE_ACTIVE" """

new_sleep = """            if False:
                pass"""

if old_sleep in content:
    content = content.replace(old_sleep, new_sleep)
    with open(filepath, 'w') as f:
        f.write(content)
    print("Sleep mode disabled locally!")
else:
    print("Sleep mode block not found.")
