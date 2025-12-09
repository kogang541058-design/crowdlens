# ⚠️ PUSHER SETUP REQUIRED

## Why Real-Time Notifications Aren't Working

Your Pusher credentials are not configured. The system is using default placeholder values.

---

## 🔧 Quick Fix (5 minutes)

### Step 1: Create Free Pusher Account

1. Go to: **https://dashboard.pusher.com/accounts/sign_up**
2. Sign up (free, no credit card needed)
3. Verify email

### Step 2: Create Pusher App

1. Click "Create app" or "Channels apps" → "Create app"
2. Fill in:
   - **Name:** `crowdlens`
   - **Cluster:** Choose closest to your location:
     - `us2` (US East)
     - `us3` (US West)
     - `eu` (Europe)
     - `ap1` (Asia Pacific)
     - `mt1` (Global/Default)
   - **Frontend tech:** JavaScript
   - **Backend tech:** Laravel
3. Click "Create app"

### Step 3: Get Your Credentials

1. In your new app, click **"App Keys"** tab
2. You'll see:
   ```
   app_id: 1234567
   key: a1b2c3d4e5f6g7h8
   secret: x9y8z7w6v5u4t3s2
   cluster: us2
   ```

### Step 4: Update .env File

Open: `C:\xampp\htdocs\crowdlens\.env`

Find these lines:
```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

Replace with YOUR actual credentials (no quotes):
```env
PUSHER_APP_ID=1234567
PUSHER_APP_KEY=a1b2c3d4e5f6g7h8
PUSHER_APP_SECRET=x9y8z7w6v5u4t3s2
PUSHER_APP_CLUSTER=us2
```

**⚠️ IMPORTANT:** Use your ACTUAL values, not the examples above!

### Step 5: Clear Cache

Run in PowerShell:
```powershell
php artisan config:clear
```

### Step 6: Test

1. Open admin page: http://localhost/crowdlens/public/admin/reports
2. Press F12 to open console
3. Look for: `✓ Pusher connected successfully`
4. Submit a test report from user dashboard
5. Should see notification instantly!

---

## 🧪 Alternative: Test Without Pusher

If you don't want to use Pusher, you can use **polling** (old method):

1. Open `.env` file
2. Change:
   ```env
   BROADCAST_CONNECTION=pusher
   ```
   To:
   ```env
   BROADCAST_CONNECTION=log
   ```

3. Run: `php artisan config:clear`

**Note:** Polling refreshes every 2 seconds (less efficient than real-time).

---

## 📊 Verify Pusher is Working

### In Browser Console (F12):
```
✓ Pusher credentials found
✓ Laravel Echo initialized successfully
✓ Subscribing to admin-notifications channel...
✓ Listening for .report.submitted events
✓ Pusher connected successfully
```

### In Pusher Dashboard:
1. Go to your app → **Debug Console** tab
2. Submit a report
3. You should see events appear in real-time:
   - `pusher:connection_established`
   - `pusher:subscription_succeeded` (admin-notifications channel)
   - `report.submitted` event with data

---

## ❌ Still Not Working?

1. **Check browser console for errors** (F12)
2. **Verify credentials** match Pusher dashboard exactly
3. **Check Laravel logs:** `storage/logs/laravel.log`
4. **Test page:** http://localhost/crowdlens/public/test-realtime

---

**Free Tier Limits:**
- ✅ 200,000 messages per day
- ✅ 100 concurrent connections
- ✅ Perfect for capstone projects!

Get your credentials now: https://dashboard.pusher.com/
