# AI Chatbot Widget - Quick Install

## Jednostavna Instalacija (2 Koraka)

### 1. Upload Folder

Upload-uj ceo `ai-bot` folder na svoj server.

**Struktura:**
```
your-website.com/
  ├── index.html
  └── ai-bot/          ← Upload-uj ovaj folder
      ├── js/
      ├── css/
      ├── api/
      └── ...
```

### 2. Dodaj u HTML

Na kraju `index.html` fajla (pre `</body>`), dodaj:

```html
<!-- AI Chatbot Widget -->
<link rel="stylesheet" href="ai-bot/css/chatbot-widget.css">
<script src="ai-bot/js/chatbot-widget.js" defer></script>
```

### 3. Gotovo! ✅

Chatbot se automatski pojavljuje u donjem desnom uglu.

---

## Kompletan Primer

```html
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Website</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Willkommen</h1>
    <p>Deine Website Inhalt...</p>
    
    <!-- AI Chatbot Widget - Am Ende hinzufügen -->
    <link rel="stylesheet" href="ai-bot/css/chatbot-widget.css">
    <script src="ai-bot/js/chatbot-widget.js" defer></script>
</body>
</html>
```

---

## Konfiguracija (Opcional)

```html
<script>
  window.CHATBOT_CONFIG = {
    apiEndpoint: 'ai-bot/api/chatbot.php',
    welcomeMessage: 'Hallo! Wie kann ich helfen?',
    placeholder: 'Nachricht schreiben...',
    primaryColor: '#667eea'
  };
</script>
<link rel="stylesheet" href="ai-bot/css/chatbot-widget.css">
<script src="ai-bot/js/chatbot-widget.js" defer></script>
```

---

## Putanje

Ako je `ai-bot` folder na drugoj lokaciji:

```html
<!-- Root level -->
<link rel="stylesheet" href="/ai-bot/css/chatbot-widget.css">
<script src="/ai-bot/js/chatbot-widget.js" defer></script>

<!-- Subfolder -->
<link rel="stylesheet" href="assets/ai-bot/css/chatbot-widget.css">
<script src="assets/ai-bot/js/chatbot-widget.js" defer></script>

<!-- Absolute URL -->
<link rel="stylesheet" href="https://cdn.example.com/ai-bot/css/chatbot-widget.css">
<script src="https://cdn.example.com/ai-bot/js/chatbot-widget.js" defer></script>
```

---

**Das war's! Einfach und schnell.** 🚀

