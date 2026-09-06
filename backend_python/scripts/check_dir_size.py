import os
import paramiko
client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'))
stdin, stdout, stderr = client.exec_command('du -ah /var/www/html/app/modules/cron/controllers/python_worker | sort -rh | head -n 10')
print("Largest files:\\n", stdout.read().decode('utf-8'))
client.close()
