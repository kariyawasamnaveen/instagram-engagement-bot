import mysql.connector

db = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="Root@123",
    database="smm_db"
)
cursor = db.cursor()

data = [
    ("NayMystique", "XZW6KTRN77NHR2ZM3IIN7CP7LP7SSUBB"),
    ("fitdiabla562", "MA7NGEZPSKYROSLENOWQHI2FLZQAKUYZ"),
    ("minipumpz", "2YUS32PLHRWSXOIXTABPCS2BAUZXKZCE"),
    ("justinleewlf93735", "XDGSXM6ZBIE27EUTYWWEXN43H5B2Z6N5"),
    ("CrowVedaVerse", "JHQ7S47FYONTCSVDOGYOO7ORUY75G6AZ"),
    ("StrathSpells", "MYXUZSQP23ZQQ22ZAQLTB7AIEVLINDUZ"),
    ("SolanaDrift", "GZJUMAFANBFP2DOZVX4EKM2NLZIZ4ICO"),
    ("CallTheVire", "C4USDIPNXYPFBA3OJ2PEYFWROAGRJ2KI"),
    ("ZyraKismet", "5RTHZBOK36BS2TP3YIV3ZV2GWBW4NYPV"),
    ("cathyarroyofit", "M5DG3PDWFO47JXSIZXERYIPBUBEXBCAU"),
    ("CassWraitheCode", "H2TKNWEHBOTLTS22V23E2VMWUMUHBSA4"),
    ("pixxydixon", "LK2OGFI7ZV4YJQ4S3IPF5FF6ABTSM4EV"),
    ("ZafiMyst", "KBKNR5WELLS2MO5PJG2QMF4F5AMHGVJ3"),
    ("GreySylasGrim", "YVQ6FKCG6K4B7S3SN7S6N5WODIOX2HJN"),
    ("ashley0wenz", "BU5BTAUQG2IWY7F6RWKRRI6KV3HNXTXQ"),
    ("melanlewalker", "YIIG2F2IFRSTJ2B7Z3NMBZUB2V76CUSI")
]

for username, secret in data:
    cursor.execute("UPDATE ig_accounts SET two_factor_secret = %s WHERE username = %s", (secret, username))
    
db.commit()
print("Updated 2FA secrets successfully.")
cursor.close()
db.close()
