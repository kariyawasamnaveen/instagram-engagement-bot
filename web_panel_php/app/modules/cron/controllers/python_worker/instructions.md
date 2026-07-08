# Python Playwright Worker - Setup Instructions 🚀

මෙන්න ඔබගේ අලුත් Python Worker එක Live Ubuntu Server (VPS) එකේ Install කරගන්නා ආකාරය පියවරෙන් පියවර:

## 1. Database (දත්ත සමුදාය) අප්ඩේට් කිරීම
මුලින්ම ඔබේ PhpMyAdmin හෝ MySQL කොන්සෝල් එකට ගිහින් පහත SQL කේතය Run කරන්න. (මෙමගින් අලුත් cookies සේව් කරගන්න තීරුව හැදෙනවා). `schema.sql` ෆයිල් එකේ මේ කේතයම තියෙනවා:
```sql
ALTER TABLE `ig_accounts` ADD COLUMN `cookies` LONGTEXT NULL AFTER `status`;
```

## 2. Python Packages Install කිරීම
ඔබේ Ubuntu සර්වර් එකට SSH හරහා (root ලෙස) ලොග් වී පහත විධානයන් (commands) Run කරන්න:
```bash
sudo apt update
sudo apt install python3 python3-pip python3-venv -y

# Virtual Environment එකක් හදාගන්න (නිවැරදි තැනකට ගිහින්)
cd /var/www/vhosts/YOUR_AIRPROXY_USER.com/httpdocs/app/modules/cron/controllers/python_worker/
python3 -m venv venv
source venv/bin/activate

# අවශ්‍ය Packages ටික ඉන්ස්ටෝල් කරන්න
pip install playwright mysql-connector-python asyncio
playwright install
playwright install-deps
```

## 3. Database විස්තර ලබා දීම
`worker.py` ෆයිල් එක open කර එහි උඩින්ම ඇති `DB_CONFIG` කොටසේ ඔබගේ Live Server එකේ ඩේටාබේස් password සහ user names නිවැරදිව ලබා දෙන්න.

## 4. Background Service එක හැදීම (Systemd)
අපි හදපු `smm-worker.service` කේතය සර්වර් එකේ `/etc/systemd/system/` ෆෝල්ඩරයට කොපි කරන්න. 
```bash
sudo nano /etc/systemd/system/smm-worker.service
# (ඔබට අවශ්‍ය නම් මෙහිදී paths වෙනස් කරගන්න)
```
සේව් කළාට පස්සේ මේවා Run කරන්න:
```bash
sudo systemctl daemon-reload
sudo systemctl enable smm-worker.service
sudo systemctl start smm-worker.service
```
ඔබගේ Python Worker එක දැන් දවසේ පැය 24 ම දුවන්න පටන් ගත්තා! 🎉

## 5. පරණ PHP Automation එක නැවැත්වීම
**මෙය ඉතා වැදගත්!** පරණ PHP කේතයත් අලුත් Python කේතයත් දෙකම එකවර දිව්වොත් ගැටලු එන්න පුළුවන්. ඒ නිසා `app/modules/cron/controllers/Automation_worker.php` ෆයිල් එකේ `run()` ෆන්ක්ෂන් එක ඇතුලත තියෙන දේවල් ඔක්කොම මකා දාලා ඒ වෙනුවට මේ ටික විතරක් දාන්න:

```php
public function run()
{
    $cron_key = get_cron_key();
    if ($cron_key != $this->input->get('key') && !is_cli()) {
        die("Cron Key mismatch.");
    }
    
    // PHP Automation is Disabled. 
    // Tasks are now handled by the Python Playwright Background Service.
    echo "Python Worker is active. PHP processing disabled.";
    return true;
}
```

මේ පියවර ටික කළාට පස්සේ ඔබගේ අලුත් Super Fast, Anti-ban Automation පද්ධතිය 100% ක් වැඩ පටන් ගන්නවා! ✨
