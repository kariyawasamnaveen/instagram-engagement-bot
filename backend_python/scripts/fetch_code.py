import os
import paramiko
import sys

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
try:
    client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'), timeout=10)
    sftp = client.open_sftp()
    sftp.get('/var/www/html/app/modules/cron/controllers/python_worker/worker_server.py', '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/remote_worker_playwright.py')
    sftp.close()
    print("Download successful")
except Exception as e:
    print(f"Error: {e}")
finally:
    client.close()
