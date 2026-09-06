import paramiko
import sys

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
try:
    client.connect('YOUR_SERVER_IP', username='root', password='YOUR_DB_PASSWORD', timeout=10)
    sftp = client.open_sftp()
    sftp.get('/var/www/html/app/modules/cron/controllers/python_worker/worker_server.py', '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/remote_worker_playwright.py')
    sftp.close()
    print("Download successful")
except Exception as e:
    print(f"Error: {e}")
finally:
    client.close()
