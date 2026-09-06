import os
import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'))

# Upload the file
sftp = client.open_sftp()
local_path = '/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/account_verifier.py'
remote_path = '/var/www/html/app/modules/cron/controllers/python_worker/account_verifier.py'
print("Uploading updated account_verifier.py...")
sftp.put(local_path, remote_path)
sftp.close()

# Restart PM2
def run_cmd(cmd):
    print(f"--- Running: {cmd} ---")
    stdin, stdout, stderr = client.exec_command(cmd)
    out = stdout.read().decode('utf-8').strip()
    if out: print(out)

print("Restarting PM2 process...")
run_cmd("pm2 restart account_verifier")
run_cmd("pm2 logs account_verifier --lines 10 --nostream")

client.close()
