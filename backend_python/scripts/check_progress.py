import os
import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'))

def run_cmd(cmd):
    print(f"--- Running: {cmd} ---")
    stdin, stdout, stderr = client.exec_command(cmd)
    out = stdout.read().decode('utf-8').strip()
    if out: print(out)

print("==== Working Accounts Count ====")
run_cmd("wc -l /var/www/html/app/modules/cron/controllers/python_worker/working_accounts.txt | awk '{print $1}'")

print("\n==== Banned/Failed Accounts Count ====")
run_cmd("wc -l /var/www/html/app/modules/cron/controllers/python_worker/banned_accounts.txt | awk '{print $1}'")

print("\n==== PM2 Verifier Logs ====")
run_cmd("pm2 logs account_verifier --lines 15 --nostream")

client.close()
