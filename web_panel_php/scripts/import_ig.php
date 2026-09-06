<?php
// DB Config
$host = '127.0.0.1'; $user = 'root'; $pass = 'Root@123'; $db = 'smm_db';
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) die("Connect failed: " . $mysqli->connect_error);

$data = <<<EOD
SolanaDrift|uqqB196Xt|GZJUMAFANBFP2DOZVX4EKM2NLZIZ4ICO|ig_did=95F51930-F234-4313-88C3-875DED245305;datr=Rq5yabT6xBkmQPoo3BbAHBiS;mid=aXKuRgABAAEOkiVdWYBeGDKMI8j4;csrftoken=8d6wpdODdGFtWsioJRAKl2lKhUMsIpLf;ds_user_id=74584980544;dpr=1;wd=524x505;sessionid=74584980544%3AWeEyVwPAFvxuSU%3A11%3AAYg7-0YYakwXESKXoLT0G4PnwHCjih52m1FpknK06D4;rur="CCO\\05474584980544\\0541806531407:01fe972f45c5ab981f86dc481cf4576096f69bd9d1df787383fc4c14d242a5217fc321c8"
MareaDeNotas|r7rNFkOGE|GUCFCIA7AOWS3J32AHNPOQ23A265UXD4|ig_did=0E830D1A-6BC2-45A4-B0EC-89950BB26215;datr=fK5yaR68Rry1alFLdgGuYaXk;mid=aXKufAABAAEYBuFXdiBtww4Gcv8Z;csrftoken=gTeL3HcsBs1VrLbmelnpJnAD2CxARMg9;ds_user_id=74935889315;dpr=1;wd=524x505;sessionid=74935889315%3ArTRnUPXEoMBetw%3A29%3AAYj89aFVqpf37w30hYUstbH9UvsWUmLUltcmzsXvFLM;rur="CCO\\05474935889315\\0541806531579:01fe8aca105774c2086f41dcfe1790dcb08ab69194f1a316234063382982e9c03cb2adab"
Octavonez|z5VQvWVPV|XG7SBMT7EDNTC6SQC7MKJDWDPE5NMHEZ|ig_did=7F4A9589-D409-4DEC-8ECD-AE6976731CAB;datr=NrByaer8ZlUIT2iXjE6NQoT9;mid=aXKwNgABAAFjP-MSSHz27mLt7iD1;csrftoken=ouSKRrOIT46TG3jzD3FFBXqRz8RCI8Oc;ds_user_id=74734926268;dpr=1;wd=524x505;sessionid=74734926268%3AzJ4W5lWYcPFjin%3A4%3AAYhIatGWcnfIRvyH4CKY0TvO27uRwXaIuGfjsIpIZho;rur="PRN\\05474734926268\\0541806532681:01fe3cc52e958633678f2453cbedb63d1daaa375dc56b83e1e25652c0224e5e61d391e71"
Yaotlique|rqS5zSPLC|6HF7EDPODZMWYGHM3HAG5MPIV5XPFVQ5|ig_did=4EB6F088-639B-47D3-A049-BF483A8DD918;datr=h7ByaT1CEdMJLKFACb4hdoSA;mid=aXKwhwABAAF5zn3a_ll_h12eiOkW;csrftoken=ydCzTssz67ahlx3ZnC8D0aShsdyXyF0D;ds_user_id=74750044484;dpr=1;wd=524x505;sessionid=74750044484%3AcuiURL9dzydN2K%3A20%3AAYilSkocRZkS40B8NeYOspIbOcqIC6T46HZvDpI2m90;rur="PRN\\05474750044484\\0541806532839:01fe537ab9ec276f1b849d41a06d68d2d884b34588d63d0f8e654d260c702f35daa2adeb"
ThayThatAgain|X6s91xElx|VFO6PARDH33LKMUO6CS4P7XHOTI2UMK6|ig_did=4CDC5F80-277B-4BC5-8E34-7C6C258135AE;datr=QLNyaQqycj6tn6LSKHA92QGX;mid=aXKzQQABAAEAZDJuLggZHZf3ULUV;csrftoken=auMuUDstuP2Bl31m95965jUoZMoTCi56;ds_user_id=74896379915;dpr=1;wd=524x505;sessionid=74896379915%3A9gSj01nCa7Pb6e%3A14%3AAYgUd4JJEvMW2ElqKVf3XIciAE-lUgOFJqpc3RYMnyw;rur="EAG\\05474896379915\\0541806534772:01feae455f122dffcab45cbdc6f26cf884ca158aa9e2b7891764cb41ea9b4e761de640cc"
EOD;

$lines = explode("\n", trim($data));
foreach ($lines as $line) {
    if (empty($line)) continue;
    $parts = explode('|', $line);
    if (count($parts) < 4) continue;

    $username = $mysqli->real_escape_string($parts[0]);
    $password = $mysqli->real_escape_string($parts[1]);
    $twofa    = $mysqli->real_escape_string($parts[2]);
    $cookie_raw = $parts[3];

    // Parse Cookies to Playwright JSON
    $cookie_list = explode(';', $cookie_raw);
    $json_cookies = [];
    foreach ($cookie_list as $c) {
        $kv = explode('=', trim($c), 2);
        if (count($kv) == 2) {
            $json_cookies[] = [
                "name" => $kv[0],
                "value" => $kv[1],
                "domain" => ".instagram.com",
                "path" => "/"
            ];
        }
    }
    $cookies_json = $mysqli->real_escape_string(json_encode($json_cookies));

    $sql = "INSERT INTO ig_accounts (username, password, cookies, notes, status, created, updated) 
            VALUES ('$username', '$password', '$cookies_json', '2FA:$twofa', 1, NOW(), NOW())";
    
    if ($mysqli->query($sql)) {
        echo "Imported: $username\n";
    } else {
        echo "Error: " . $mysqli->error . "\n";
    }
}
$mysqli->close();
?>
