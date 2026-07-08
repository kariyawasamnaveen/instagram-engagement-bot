import os
import time
import requests
import random
from instagrapi import Client

PROXY_SETTINGS = {
    'server': 'http://YOUR_AIRPROXY_SERVER:11001',
    'username': 'YOUR_AIRPROXY_USER',
    'password': 'YOUR_AIRPROXY_PASS_1'
}

def rotate_proxy():
    print("[PROXY] Rotating AirProxy IP...")
    try:
        res = requests.get('https://airproxy.io/api/proxy/change_ip/?format=json&id=3301&key=YOUR_AIRPROXY_API_KEY', timeout=10)
        if res.status_code == 200:
            print("[PROXY] Successfully rotated IP. Waiting 15 seconds for stabilization...")
            time.sleep(15)
            return True
        else:
            print(f"[PROXY] Failed to rotate. Status code: {res.status_code}")
            return False
    except Exception as e:
        print(f"[PROXY] Rotation error: {e}")
        return False

def get_proxy_url():
    return f"http://{PROXY_SETTINGS['username']}:{PROXY_SETTINGS['password']}@YOUR_AIRPROXY_SERVER:11001"

def process_accounts(input_file, working_file, banned_file):
    with open(input_file, 'r', encoding='utf-8') as f:
        lines = [line.strip() for line in f if line.strip()]
        
    print(f"Loaded {len(lines)} accounts to verify.")
    
    # We want to rotate proxy immediately before the first batch
    rotate_proxy()
    
    for idx, line in enumerate(lines):
        if idx > 0 and idx % 4 == 0:
            print(f"--- Processed {idx} accounts. Rotating proxy to prevent bans... ---")
            rotate_proxy()
            
        parts = line.split('|')
        auth_part = parts[0]
        # format: +916366180371:636618
        if ':' not in auth_part:
            print(f"[{idx+1}/{len(lines)}] Invalid format: {auth_part}")
            continue
            
        username, password = auth_part.split(':', 1)
        print(f"\n[{idx+1}/{len(lines)}] Checking {username}...")
        
        cl = Client()
        cl.request_timeout = 30  # Add timeout so it doesn't hang forever
        
        # Override the challenge handler so it doesn't prompt for terminal input and hang
        def auto_fail_challenge(username, choice):
            raise Exception("Challenge required (SMS/Email). Skipping.")
        cl.challenge_code_handler = auto_fail_challenge
        
        cl.set_proxy(get_proxy_url())
        
        try:
            # Random delay before login to mimic human pacing
            time.sleep(random.uniform(2, 5))
            login_result = cl.login(username, password)
            if login_result:
                print(f"[SUCCESS] {username} is WORKING.")
                with open(working_file, 'a', encoding='utf-8') as wf:
                    wf.write(f"{username}:{password}\n")
            else:
                print(f"[FAILED] {username} login returned False.")
                with open(banned_file, 'a', encoding='utf-8') as bf:
                    bf.write(f"{username}:{password} - Login returned False\n")
                    
        except Exception as e:
            err_msg = str(e).lower()
            if any(term in err_msg for term in ['challenge', 'checkpoint', 'consent', 'suspended', 'deactivated']):
                print(f"[BANNED/CHALLENGE] {username} is blocked: {str(e)[:50]}")
                with open(banned_file, 'a', encoding='utf-8') as bf:
                    bf.write(f"{username}:{password} - {str(e)[:50]}\n")
            else:
                print(f"[ERROR] {username} failed with unexpected error: {str(e)[:50]}")
                with open(banned_file, 'a', encoding='utf-8') as bf:
                    bf.write(f"{username}:{password} - ERROR: {str(e)[:50]}\n")
                    
if __name__ == '__main__':
    # Local paths on VPS
    INPUT_FILE = '/var/www/html/app/modules/cron/controllers/python_worker/goodAAAL.txt'
    WORKING_FILE = '/var/www/html/app/modules/cron/controllers/python_worker/working_accounts.txt'
    BANNED_FILE = '/var/www/html/app/modules/cron/controllers/python_worker/banned_accounts.txt'
    
    # Initialize files
    for f in [WORKING_FILE, BANNED_FILE]:
        if not os.path.exists(f):
            with open(f, 'w') as fh:
                pass
                
    process_accounts(INPUT_FILE, WORKING_FILE, BANNED_FILE)
    print("Verification completed.")
