# Instagram Engagement Bot (SmartPanel Ecosystem)

An advanced, automated Instagram SMM (Social Media Marketing) panel backend and mobile application ecosystem. This project features Android app emulation (Instagrapi), human-like account warm-up sequences, automated likes/follows processing, proxy rotation, and bulk account verification to bypass anti-bot detections.

## Project Structure
This repository contains the complete ecosystem divided into three main components:

1. **`backend_python/`**: The Python-based automation engine. Handles Instagram logins, executing likes/follows, account warm-ups, and bulk verifications using the `instagrapi` library and AirProxy for IP rotation.
2. **`web_panel_php/`**: The SmartPanel PHP frontend and API layer. This is where users place orders and where the Python bots fetch their tasks.
3. **`mobile_app_flutter/`**: The cross-platform mobile application built with Flutter/Dart for managing the panel on the go.

## Features
* **Android Emulation**: Uses `instagrapi` to mimic legitimate Android devices, drastically reducing blocks compared to web-based automation.
* **Smart Proxy Rotation**: Integrates with AirProxy API to automatically change the IP address between actions to evade location-based detection.
* **Warm-up Engine**: Automatically ages new accounts by scrolling feeds and watching reels like a human before they perform heavy actions.
* **Bulk Account Verifier**: Tests hundreds of accounts automatically, filtering out banned or SMS-challenged accounts.

## Getting Started (Backend)

### Prerequisites
* Python 3.9+
* PM2 (Process Manager)
* AirProxy Subscription (or similar rotating proxy)
* MySQL Database

### Installation
1. Navigate to the backend directory:
   ```bash
   cd backend_python
   ```
2. Install the required Python packages:
   ```bash
   pip install -r requirements.txt
   ```
   *(Note: Ensure you install `instagrapi`, `requests`, `mysql-connector-python`, etc.)*

3. **Configuration**: Update your database credentials and AirProxy keys in the worker scripts. Search for placeholders like `YOUR_DB_PASSWORD` and `YOUR_AIRPROXY_API_KEY` and replace them with your actual credentials.

4. **Running the Bots**:
   Use PM2 to run the scripts continuously in the background:
   ```bash
   pm2 start api_worker_server.py --name "IG_Main_Worker"
   pm2 start warmup_server.py --name "IG_Warmup_Bot"
   ```

## Disclaimer
This project was developed for educational and automation testing purposes. Please use it responsibly and in accordance with Instagram's Terms of Service.
