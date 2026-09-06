import paramiko
import os

local_path = '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/remote_worker_updated.py'
remote_path = '/var/www/html/app/modules/cron/controllers/python_worker/worker_server.py'

transport = paramiko.Transport(('YOUR_SERVER_IP', 22))
transport.connect(username='root', password='YOUR_DB_PASSWORD')
sftp = paramiko.SFTPClient.from_transport(transport)

print(f"Uploading {local_path} to {remote_path}...")
sftp.put(local_path, remote_path)
sftp.close()
transport.close()
print("Upload complete.")
