import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('YOUR_SERVER_IP', username='root', password='YOUR_DB_PASSWORD')

def run_cmd(cmd):
    print(f"--- Running: {cmd} ---")
    stdin, stdout, stderr = client.exec_command(cmd)
    out = stdout.read().decode('utf-8')
    err = stderr.read().decode('utf-8')
    if out: print(out.strip())
    if err: print("ERR:", err.strip())

run_cmd("cd /var/www/html/app/modules/cron/controllers/python_worker && pm2 start account_verifier.py --name account_verifier")
run_cmd("pm2 logs account_verifier --lines 10 --nostream")

client.close()
