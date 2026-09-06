import requests

proxies = {
    'http': 'http://YOUR_AIRPROXY_USER:YOUR_AIRPROXY_PASS_1@YOUR_AIRPROXY_SERVER:11001',
    'https': 'http://YOUR_AIRPROXY_USER:YOUR_AIRPROXY_PASS_1@YOUR_AIRPROXY_SERVER:11001'
}

try:
    print("Testing proxy connection from local machine...")
    res = requests.get('https://api.ipify.org?format=json', proxies=proxies, timeout=15)
    print("STATUS:", res.status_code)
    print("RESPONSE:", res.json())
except Exception as e:
    print("PROXY ERROR:", e)
