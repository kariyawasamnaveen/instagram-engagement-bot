import paramiko
import os

local_path = '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/checkpoint_solver.py'
remote_path = '/var/www/html/app/modules/cron/controllers/python_worker/checkpoint_solver.py'

transport = paramiko.Transport((os.getenv('SERVER_IP', '127.0.0.1'), 22))
transport.connect(username='root', password=os.getenv('DB_PASS', 'secret'))
sftp = paramiko.SFTPClient.from_transport(transport)

print(f"Uploading {local_path} to {remote_path}...")
sftp.put(local_path, remote_path)
sftp.close()
transport.close()
print("Upload complete.")
