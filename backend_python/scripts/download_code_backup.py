import os
import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'))

print("Zipping python and txt files on the server (excluding cache/profiles)...")
# Find and zip only .py and .txt files, excluding the profiles directory
cmd = 'cd /var/www/html/app/modules/cron/controllers && find python_worker -name "*.py" -o -name "*.txt" | grep -v "python_worker/profiles" | zip python_worker_code_backup.zip -@'
stdin, stdout, stderr = client.exec_command(cmd)
status = stdout.channel.recv_exit_status()

if status != 0 and status != 12: # zip returns 12 if it has nothing to do, but we should have files
    print(f"Error creating zip: {stderr.read().decode('utf-8')}")
else:
    sftp = client.open_sftp()
    local_path = '/Users/n.skariyawasam/Desktop/SmartPanel_Code_Backup.zip'
    remote_path = '/var/www/html/app/modules/cron/controllers/python_worker_code_backup.zip'
    
    print(f"Downloading code backup to: {local_path}")
    try:
        sftp.get(remote_path, local_path)
        print("Cleaning up server...")
        sftp.remove(remote_path)
        print("SUCCESS! Code Backup downloaded.")
    except Exception as e:
        print(f"Error downloading: {e}")
    finally:
        sftp.close()

client.close()
