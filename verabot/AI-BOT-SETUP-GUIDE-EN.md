# AI Chatbot Widget - Complete Setup Guide

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Customizing Chatbot Information](#customizing-chatbot-information)
5. [Integration](#integration)
6. [Testing](#testing)
7. [Troubleshooting](#troubleshooting)

---

## Prerequisites

- ✅ **Web Server** with PHP 7.4 or higher
- ✅ **OpenAI API Account** with credits
- ✅ **Valid License Key** (received via email)
- ✅ **FTP/SSH Access** to your server

---

## Installation

### Step 1: Download Package

You received a customized package with your company name. The package is locked to your company name and cannot be changed.

1. Use the download link from your email
2. Enter the download password
3. Download the ZIP file

### Step 2: Extract Files

Extract the ZIP file. You'll see:
```
ai-bot-[YOUR-COMPANY-NAME]/
├── api.php
├── config.php (pre-configured with your company name - LOCKED)
├── css/
├── js/
├── INSTALLATION-GUIDE.md
└── ...
```

**Important:** The company name is locked in `config.php` and cannot be changed. This is for license protection.

### Step 3: Upload to Server

1. Upload the entire `ai-bot-[YOUR-COMPANY-NAME]` folder to your server
2. Can be placed anywhere (root, subfolder, etc.)
3. Note the folder path (you'll need it for integration)

---

## Configuration

### Step 1: Configure OpenAI API Key

**Option A: .env File (Recommended)**

1. Create `.env` file in the `ai-bot-[YOUR-COMPANY-NAME]` folder
2. Add:
```
OPENAI_API_KEY=sk-your-api-key-here
```
3. Replace with your actual OpenAI API key

**How to Get OpenAI API Key:**
1. Go to [https://platform.openai.com](https://platform.openai.com)
2. Sign up or log in
3. Go to **Settings** → **API Keys**
4. Click **Create new secret key**
5. Copy the key (starts with `sk-`)
6. Add payment method if required

**Option B: Edit api.php**

Open `api.php` and find:
```php
$OPENAI_API_KEY = getenv('OPENAI_API_KEY') ?: '';
```

Replace with:
```php
$OPENAI_API_KEY = 'sk-your-api-key-here';
```

### Step 2: Review Company Information

The `config.php` file is pre-configured with your company name (LOCKED). You can edit:

- ✅ **Services** - Add/remove services
- ✅ **Contact Information** - Update email, phone
- ✅ **About Section** - Company description
- ✅ **Team Information** - Staff details
- ✅ **Specialization** - What you're best at

**⚠️ DO NOT CHANGE:**
- ❌ Company name (locked for license protection)
- ❌ License verification code
- ❌ License key

---

## Customizing Chatbot Information

### Adding Company Services

Edit `config.php`, find `services` array:

```php
'services' => [
    'Renovierung',
    'Handwerk',
    'Bauarbeiten',
    // Add your services here
],
```

### Updating Contact Information

```php
'contact' => [
    'email' => 'info@yourcompany.com',
    'phone' => '+49 123 456 789',
    'address' => 'Your Address',
],
```

### Setting Company Description

```php
'about' => 'We are a professional company specializing in...',
```

### Adding Team Information

```php
'team' => [
    'Founder Name' => 'Role/Position',
    // Add more team members
],
```

### Defining Specialization

```php
'specialization' => 'We specialize in high-quality renovations and construction work...',
```

**After editing, save the file.** Changes take effect immediately.

---

## Integration

### Simple Integration (2 Lines of Code)

Add these two lines to your HTML file (before closing `</body>` tag):

```html
<link rel="stylesheet" href="ai-bot-[YOUR-FOLDER-NAME]/css/chatbot-widget.css">
<script src="ai-bot-[YOUR-FOLDER-NAME]/js/chatbot-widget.js" defer></script>
```

**Replace `ai-bot-[YOUR-FOLDER-NAME]` with your actual folder name.**

### Complete Example

```html
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Your Website</title>
    <!-- Your CSS -->
</head>
<body>
    <!-- Your website content -->
    
    <!-- AI Chatbot Widget - Add at the end -->
    <link rel="stylesheet" href="ai-bot-[YOUR-FOLDER-NAME]/css/chatbot-widget.css">
    <script src="ai-bot-[YOUR-FOLDER-NAME]/js/chatbot-widget.js" defer></script>
</body>
</html>
```

### Different Folder Locations

**If chatbot folder is in root:**
```html
<link rel="stylesheet" href="/ai-bot-[YOUR-FOLDER-NAME]/css/chatbot-widget.css">
<script src="/ai-bot-[YOUR-FOLDER-NAME]/js/chatbot-widget.js" defer></script>
```

**If chatbot folder is in subfolder:**
```html
<link rel="stylesheet" href="assets/ai-bot-[YOUR-FOLDER-NAME]/css/chatbot-widget.css">
<script src="assets/ai-bot-[YOUR-FOLDER-NAME]/js/chatbot-widget.js" defer></script>
```

---

## Testing

### Test Chatbot

1. Open your website in a browser
2. Look for chatbot icon in bottom-right corner
3. Click the icon
4. Type a test message (e.g., "Hello")
5. Wait for AI response (usually 2-5 seconds)

### Test Company Information

Ask the chatbot:
- "What services do you offer?"
- "What is your company name?"
- "How can I contact you?"
- "Tell me about your company"

The chatbot should respond with information from `config.php`.

---

## Troubleshooting

### Chatbot Not Appearing

**Check:**
1. ✅ CSS and JS file paths are correct
2. ✅ Files are uploaded to correct location
3. ✅ No JavaScript errors in browser console (F12)

**Solution:**
- Verify folder path matches in HTML
- Check file permissions (should be 644)
- Clear browser cache

### "API Error" Messages

**Check:**
1. ✅ OpenAI API key is configured
2. ✅ API key is valid (starts with `sk-`)
3. ✅ OpenAI account has credits

**Solution:**
- Verify `.env` file or `api.php` has correct API key
- Check OpenAI dashboard for credit balance
- Test API key at platform.openai.com

### Chatbot Not Responding Correctly

**Check:**
1. ✅ `config.php` has company information
2. ✅ Services are listed in config
3. ✅ Contact information is updated

**Solution:**
- Review and update `config.php`
- Ensure all fields are filled correctly
- Save file after changes

### License Issues

**If you see license error:**
1. Verify license key matches your domain
2. Contact support with your license key
3. Company name cannot be changed (locked for protection)

---

## Security Notes

### Password Protection

Your chatbot package may include password protection:

1. **Admin Panel:** `chat-admin.php` is password protected
2. **Config File:** `config.php` contains sensitive information - keep it secure
3. **API Key:** Never share your OpenAI API key

### Best Practices

✅ **DO:**
- Keep `config.php` file secure
- Regularly update company information (except company name)
- Monitor OpenAI API usage
- Keep chatbot files up to date

❌ **DON'T:**
- Share your API key publicly
- Modify company name in config (LOCKED - will break license)
- Remove license verification code
- Commit sensitive files to version control

---

## Support

If you need help:

1. **Review this guide** for common solutions
2. **Check error messages** in browser console (F12)
3. **Contact support:**
   - Email: info@verait.de
   - Website: www.verait.de

Please provide:
- Your license key (first 4 chars: XXXX-...)
- Error message screenshot
- Server information (PHP version)

---

## Important Notes

⚠️ **Company Name Locked:**
- The company name in `config.php` is locked and cannot be changed
- This is for license protection
- If you try to change it, the license will fail validation
- If you need to change it, contact support

⚠️ **Single Website License:**
- This chatbot is licensed for ONE website only
- Domain is bound to license automatically
- Cannot be transferred without approval

---

**Congratulations!** Your AI Chatbot is now ready to assist your website visitors. 🚀
