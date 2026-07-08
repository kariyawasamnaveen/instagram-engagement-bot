import requests
import concurrent.futures
import urllib.parse
import os
import json

def check_account(line):
    line = line.strip()
    if not line:
        return None
    
    parts = line.split('|')
    if len(parts) < 4:
        return None
        
    creds = parts[0]
    user_agent = parts[1]
    
    # Try to find sessionid or Bearer token
    auth_part = parts[3]
    
    headers = {
        'User-Agent': user_agent,
        'Accept': '*/*',
        'Accept-Language': 'en-US,en;q=0.5',
    }
    
    cookies = {}
    
    if auth_part.startswith('sessionid='):
        # Extract sessionid
        session_str = auth_part.split(';')[0].replace('sessionid=', '')
        # Unquote if necessary
        session_str = urllib.parse.unquote(session_str)
        cookies['sessionid'] = session_str
    elif auth_part.startswith('Authorization=Bearer'):
        token = auth_part.replace('Authorization=', '').split(';')[0]
        headers['Authorization'] = token
    else:
        return (line, False, "No valid auth found")
        
    try:
        # A lightweight endpoint to check login status
        response = requests.get(
            'https://i.instagram.com/api/v1/accounts/current_user/?edit=true',
            headers=headers,
            cookies=cookies,
            timeout=10
        )
        
        # If the response is 200 OK and contains a user dict, it's valid
        if response.status_code == 200:
            try:
                data = response.json()
                if 'user' in data:
                    return (line, True, "OK")
            except:
                pass
        
        # Any other response (401, 403, etc.) usually means expired or checkpoint
        return (line, False, f"HTTP {response.status_code}")
        
    except Exception as e:
        return (line, False, str(e))

def main():
    input_file = '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/goodAAAL.txt'
    working_file = '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/working_accounts.txt'
    dead_file = '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/dead_accounts.txt'
    
    with open(input_file, 'r', encoding='utf-8') as f:
        lines = f.readlines()
        
    print(f"Loaded {len(lines)} accounts for checking. Starting checker...")
    
    working_count = 0
    dead_count = 0
    
    with open(working_file, 'w', encoding='utf-8') as wf, open(dead_file, 'w', encoding='utf-8') as df:
        with concurrent.futures.ThreadPoolExecutor(max_workers=10) as executor:
            results = executor.map(check_account, lines)
            
            for result in results:
                if result is None:
                    continue
                
                line, is_working, reason = result
                
                if is_working:
                    wf.write(line + '\n')
                    working_count += 1
                else:
                    df.write(line + '\n')
                    dead_count += 1
                    
                total_checked = working_count + dead_count
                if total_checked % 50 == 0:
                    print(f"Checked {total_checked}/{len(lines)} | Working: {working_count} | Dead: {dead_count}")

    print(f"\\n--- FINAL RESULTS ---")
    print(f"Total Accounts: {len(lines)}")
    print(f"Working Accounts: {working_count}")
    print(f"Dead/Expired Accounts: {dead_count}")
    print(f"Working accounts saved to: {working_file}")
    print(f"Dead accounts saved to: {dead_file}")

if __name__ == '__main__':
    main()
