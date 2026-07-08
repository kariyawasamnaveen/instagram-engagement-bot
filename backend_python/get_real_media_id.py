import requests
import re

url = 'https://www.instagram.com/reel/DZgTt59mekT/'
headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'
}

proxies = {
    'http': 'http://YOUR_AIRPROXY_USER:YOUR_AIRPROXY_PASS_1@YOUR_AIRPROXY_SERVER:11001',
    'https': 'http://YOUR_AIRPROXY_USER:YOUR_AIRPROXY_PASS_1@YOUR_AIRPROXY_SERVER:11001'
}

try:
    print("Fetching page via proxy...")
    res = requests.get(url, headers=headers, proxies=proxies, timeout=20)
    print("Status:", res.status_code)
    
    html = res.text
    # Search for media ID in HTML
    matches = re.findall(r'"media_id":"(\d+)"', html)
    if matches:
        print("Found media_id matches:", set(matches))
    else:
        print("media_id not found in HTML via 'media_id'. Searching for 'instagram://media\?id='...")
        matches_app = re.findall(r'instagram://media\?id=(\d+)', html)
        if matches_app:
            print("Found via app link:", set(matches_app))
        else:
            # Let's save a part of the html or search for general id
            print("Trying general search...")
            # Look for "id":"<id>" inside script tags
            matches_id = re.findall(r'"id":"(\d{15,25})"', html)
            print("Found general ID matches:", set(matches_id))
except Exception as e:
    print("Error:", e)
