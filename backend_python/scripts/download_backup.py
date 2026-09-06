import paramiko
import os

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

print("Connecting to server...")
client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'))

# Create zip on server
print("Zipping files on the server (this might take a few seconds)...")
stdin, stdout, stderr = client.exec_command('cd /var/www/html/app/modules/cron/controllers && zip -r python_worker_backup.zip python_worker/')
stdout.channel.recv_exit_status() # Wait for the zip command to finish

# Download the zip
sftp = client.open_sftp()
local_path = '/Users/n.skariyawasam/Desktop/SmartPanel_Server_Backup.zip'
remote_path = '/var/www/html/app/modules/cron/controllers/python_worker_backup.zip'

print(f"Downloading backup to: {local_path}")
sftp.get(remote_path, local_path)

# Cleanup server
print("Cleaning up temporary zip file from server...")
sftp.remove(remote_path)

sftp.close()
client.close()

print("\nSUCCESS! Backup downloaded.")
print(f"You can find your files at: {local_path}")
