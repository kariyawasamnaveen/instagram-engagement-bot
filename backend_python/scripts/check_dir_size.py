import paramiko
client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('YOUR_SERVER_IP', username='root', password='YOUR_DB_PASSWORD')
stdin, stdout, stderr = client.exec_command('du -ah /var/www/html/app/modules/cron/controllers/python_worker | sort -rh | head -n 10')
print("Largest files:\\n", stdout.read().decode('utf-8'))
client.close()
