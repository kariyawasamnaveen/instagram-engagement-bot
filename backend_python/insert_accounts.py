import mysql.connector
import datetime

db = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="Root@123",
    database="smm_db"
)
cursor = db.cursor()

data = """Cironexo|LC7h0NT4s|[{"name":"dpr","value":"1","domain":".instagram.com","path":"/","expires":1780310292,"httpOnly":false,"secure":true,"sameSite":"None"},{"name":"csrftoken","value":"pWr7RH6ckhjbz5ByVre0dw1xEUHpr2bW","domain":".instagram.com","path":"/","expires":1814265499.205128,"httpOnly":false,"secure":true,"sameSite":"Lax"},{"name":"ds_user_id","value":"74880974590","domain":".instagram.com","path":"/","expires":1787481499.205275,"httpOnly":false,"secure":true,"sameSite":"None"},{"name":"wd","value":"524x505","domain":".instagram.com","path":"/","expires":1780310292,"httpOnly":false,"secure":true,"sameSite":"Lax"},{"name":"fr","value":"0UA2zjinOO3uH59Ej.AWcq8cLRfjRJDshMCRwoWSM07DKHvuk1kXs2Lv-PuuXvj83n-OM.BqFAKp..AAA.0.0.BqFAKp.AWfLIdaBK2Iga1F0OGFI4Lhm6bI","domain":".facebook.com","path":"/","expires":1787472295.321216,"httpOnly":true,"secure":true,"sameSite":"None"},{"name":"ig_did","value":"3C621B86-4F1C-45C3-A6B8-27558B8E6B06","domain":".instagram.com","path":"/","expires":1811241489.893802,"httpOnly":true,"secure":true,"sameSite":"Lax"},{"name":"mid","value":"ahQmkwABAAHq0z_3gapKK_eLqdm9","domain":".instagram.com","path":"/","expires":1814265492,"httpOnly":false,"secure":true,"sameSite":"Lax"},{"name":"sessionid","value":"74880974590%3AjZmwJuAiNxvoxw%3A8%3AAYgd74DQjhzAWjV-HdBy6RoVO-ebT7jW5sKbGEEgm44","domain":".instagram.com","path":"/","expires":1811241498.558501,"httpOnly":true,"secure":true,"sameSite":"Lax"},{"name":"rur","value":"\\"NHA\\\\05474880974590\\\\0541811241501:01fe83cf363bec6cb9836cb0cc60db06887b4ec9af48a9cca2bdf19601b9be78d49c3338\\"","domain":".instagram.com","path":"/","expires":-1,"httpOnly":true,"secure":true,"sameSite":"Lax"}]||jameswilliamspzx85483@daouse.com
Leonithyl|3W8SRfRU4|[]||laurenmorrissry92613@xkxkud.com
ZolAtemporal|DBMg8sJDY|[]||josephmoorefcd79341@jkotypc.com
Noevahn|Ts2xvuh6a|[]||jonathanthomasikw14349@jkotypc.com
VozDeLumbre|cxVegXXwB|[]||michaeljonesdmj31580@qzueos.com
ResonanciaMorena|bwTvBT3nA|[]||jessicasilvatns81477@xkxkud.com
SombraDelTambor|DVNP5BPnz|[]||zacharygibsonvpw61841@cmhvzylmfc.com
CantoDeObsidiana|datdeptrai46Vdw|[]||alexanderperezaak21662@mkzaso.com
BrumaSonriente|y24oc4sLH|[{"name":"ds_user_id","value":"74884526100","domain":".instagram.com","path":"/","expires":1787482194.911458,"httpOnly":false,"secure":true,"sameSite":"None"},{"name":"wd","value":"524x505","domain":".instagram.com","path":"/","expires":1780310988,"httpOnly":false,"secure":true,"sameSite":"Lax"},{"name":"fr","value":"0TfGYgla58YisFIRs.AWedzKodWrX47Gihdqy_2h3V2aNjXFhU1lHWaeSQZ0nOaG8bDQg.BqFAJw..AAA.0.0.BqFAJw.AWd_W1N6I6Dh9kowsRk2CLibqBQ","domain":".facebook.com","path":"/","expires":1787472238.965004,"httpOnly":true,"secure":true,"sameSite":"None"},{"name":"datr","value":"nSYUagoSj7ccEHFNmajI5RqF","domain":".instagram.com","path":"/","expires":1814265499.830072,"httpOnly":true,"secure":true,"sameSite":"None"},{"name":"ig_did","value":"C102B9BC-1713-4254-9969-0B11BEE41C7A","domain":".instagram.com","path":"/","expires":1811241499.830121,"httpOnly":true,"secure":true,"sameSite":"None"},{"name":"mid","value":"ahQmnQABAAGIDBSzI-hfeOXXFE6G","domain":".instagram.com","path":"/","expires":1814265500,"httpOnly":false,"secure":true,"sameSite":"Lax"},{"name":"csrftoken","value":"uHRmQsmvKl7pdfsQZu2ghS9zHzw6vMP6","domain":".instagram.com","path":"/","expires":1814266194.91129,"httpOnly":false,"secure":true,"sameSite":"Lax"},{"name":"sessionid","value":"74884526100%3ARVlEywrrGC6g1i%3A22%3AAYiBzvM-oq8rXeQdzlVPZnIyrv7-kH-vPbaP37vhRg","domain":".instagram.com","path":"/","expires":1811241566.330003,"httpOnly":true,"secure":true,"sameSite":"None"},{"name":"rur","value":"\\"NHA\\\\05474884526100\\\\0541811242197:01fe9b03effdf43806f5907d05d77e1504a23c9239e3095f3c6b82491669716bd94b5b4f\\"","domain":".instagram.com","path":"/","expires":-1,"httpOnly":true,"secure":true,"sameSite":"Lax"}]||heatherwebbwhi81388@zudpck.com
Baltareon|W5PotD85k|[]||taraphillipsmgd42580@daouse.com"""

now = datetime.datetime.now()
inserted = 0

for line in data.split('\n'):
    line = line.strip()
    if not line: continue
    parts = line.split('|')
    if len(parts) >= 5:
        username = parts[0]
        password = parts[1]
        cookies = parts[2]
        twofa = parts[3] if parts[3] else None
        email = parts[4]
        
        status = 1
        if cookies == '[]' or not cookies:
            status = 2 # Needs login
            cookies = None
            
        cursor.execute("SELECT id FROM ig_accounts WHERE username=%s", (username,))
        row = cursor.fetchone()
        if row:
            cursor.execute('''
                UPDATE ig_accounts 
                SET password=%s, cookies=%s, two_factor_secret=%s, email=%s, gender='male', status=%s 
                WHERE id=%s
            ''', (password, cookies, twofa, email, status, row[0]))
        else:
            cursor.execute('''
                INSERT INTO ig_accounts (username, password, cookies, two_factor_secret, email, gender, status, created)
                VALUES (%s, %s, %s, %s, %s, 'male', %s, %s)
            ''', (username, password, cookies, twofa, email, status, now))
        inserted += 1

db.commit()
print(f"Successfully processed {inserted} accounts.")
cursor.close()
db.close()
