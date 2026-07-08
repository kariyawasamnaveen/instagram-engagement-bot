import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

try:
    client.connect('YOUR_SERVER_IP', username='root', password='YOUR_DB_PASSWORD', timeout=10)
    
    query = """mysql -u root -pRoot@123 smm_db -e "
    SELECT username, password, cookies FROM ig_accounts WHERE username IN ('+919686085929', '+919810259801');
    " """
    
    stdin, stdout, stderr = client.exec_command(query, timeout=10)
    
    out = stdout.read().decode()
    err = stderr.read().decode()
    
    print("Output:")
    print(out)
    if err:
        print("Error:")
        print(err)
        
finally:
    client.close()
