import os
import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'))

print("Tarring files on the server...")
stdin, stdout, stderr = client.exec_command('cd /var/www/html/app/modules/cron/controllers && tar -czf python_worker_backup.tar.gz python_worker/')
status = stdout.channel.recv_exit_status()

if status != 0:
    print(f"Error creating tar: {stderr.read().decode('utf-8')}")
else:
    sftp = client.open_sftp()
    local_path = '/Users/n.skariyawasam/Desktop/SmartPanel_Server_Backup.tar.gz'
    remote_path = '/var/www/html/app/modules/cron/controllers/python_worker_backup.tar.gz'
    
    print(f"Downloading backup to: {local_path}")
    try:
        sftp.get(remote_path, local_path)
        print("Cleaning up server...")
        sftp.remove(remote_path)
        print("SUCCESS! Backup downloaded.")
    except Exception as e:
        print(f"Error downloading: {e}")
    finally:
        sftp.close()

client.close()
