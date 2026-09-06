import os
import paramiko
client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(os.getenv('SERVER_IP', '127.0.0.1'), username='root', password=os.getenv('DB_PASS', 'secret'))

def run_cmd(cmd, title):
    print(f"\n=== {title} ===")
    stdin, stdout, stderr = client.exec_command(cmd)
    out = stdout.read().decode('utf-8')
    err = stderr.read().decode('utf-8')
    if out:
        print(out.strip())
    if err:
        print("ERRORS:", err.strip())

transport = paramiko.Transport((os.getenv('SERVER_IP', '127.0.0.1'), 22))
transport.connect(username='root', password=os.getenv('DB_PASS', 'secret'))
sftp = paramiko.SFTPClient.from_transport(transport)
sftp.put('/Users/n.skariyawasam/Documents/SmartPanel_Server/Installation/get_real_media_id.py', '/tmp/get_real_media_id.py')
sftp.close()
transport.close()

run_cmd('python3 /tmp/get_real_media_id.py', 'RUNNING MEDIA ID CHECK')

client.close()






