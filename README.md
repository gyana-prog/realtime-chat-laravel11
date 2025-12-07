@"
# 🚀 Real-time Chat  - Laravel + Reverb WebSocket

## ✨ Features
- ✅ Real-time WebSocket (Laravel Reverb)
- ✅ Queue processing (background)
- ✅ Multi-tab sync (toOthers())
- ✅ Responsive chat bubbles + timestamps

## 🛠️ Quick Start - 5 Minutes Setup

### 1. Clone & Install
``````bash
git clone https://github.com/YOUR_USERNAME/realtime-chat.git
cd realtime-chat
composer install
cp .env.example .env
php artisan key:generate
2. Database Setup (MySQL)
# Edit .env:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chat_app
DB_USERNAME=root
DB_PASSWORD=

# Create MySQL database
mysql -u root -p
CREATE DATABASE chat_app;
exit

# Run migrations
php artisan migrate

3. CRITICAL - Add Reverb Config to .env
# Add these EXACT lines to your .env file:
REVERB_APP_ID=local
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
4. Run 4 Terminals (Copy-Paste Each)
# TERMINAL 1: Laravel Server
php artisan serve

# TERMINAL 2: Queue Worker
php artisan queue:work

# TERMINAL 3: Reverb WebSocket
php artisan reverb:start

# TERMINAL 4: Open Browser
http://127.0.0.1:8000/


Test LIVE Chat
1. Open 3 tabs: http://127.0.0.1:8000/
2. Tab1: Sender ID=123 → Type "Hello World!" → Send
3. Tab2/Tab3: See GREEN bubble INSTANTLY! ✅
4. Console (F12): See "✅ CONNECTED" + "🌐 LIVE MESSAGE"
