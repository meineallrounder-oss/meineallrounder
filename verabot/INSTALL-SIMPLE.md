# AI Chatbot Widget - Jednostavna Instalacija

## Kako Instalirati (3 Koraka)

### 1. Upload Folder

Upload-uj ceo `ai-bot` folder na svoj server (može bilo gde).

**Primer strukture:**
```
your-website.com/
  ├── index.html
  ├── css/
  ├── js/
  └── ai-bot/          ← Upload-uj ceo folder ovde
      ├── js/
      ├── css/
      └── api/
```

### 2. Dodaj u HTML

Na kraju svog `index.html` fajla (pre zatvaranja `</body>` taga), dodaj:

```html
<!-- AI Chatbot Widget -->
<link rel="stylesheet" href="ai-bot/css/chatbot-widget.css">
<script src="ai-bot/js/chatbot-widget.js" defer></script>
```

**Ako je folder na drugoj lokaciji, promeni putanju:**
```html
<link rel="stylesheet" href="/path/to/ai-bot/css/chatbot-widget.css">
<script src="/path/to/ai-bot/js/chatbot-widget.js" defer></script>
```

### 3. Gotovo! ✅

Chatbot će se automatski pojaviti u donjem desnom uglu sajta.

---

## Opcionalno: Konfiguracija

Ako želiš da konfigurišeš chatbot, dodaj config script:

```html
<!-- AI Chatbot Widget Config (opciono) -->
<script>
  window.CHATBOT_CONFIG = {
    apiEndpoint: 'ai-bot/api/chatbot.php',
    welcomeMessage: 'Hallo! Wie kann ich Ihnen helfen?',
    placeholder: 'Schreiben Sie eine Nachricht...',
    primaryColor: '#667eea',
    position: 'bottom-right'
  };
</script>

<!-- AI Chatbot Widget -->
<link rel="stylesheet" href="ai-bot/css/chatbot-widget.css">
<script src="ai-bot/js/chatbot-widget.js" defer></script>
```

---

## Primer Kompletnog HTML-a

```html
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Website</title>
    <!-- Tvoj CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Tvoj sadržaj -->
    <h1>Willkommen auf meiner Website</h1>
    
    <!-- AI Chatbot Widget - Dodaj na kraju -->
    <link rel="stylesheet" href="ai-bot/css/chatbot-widget.css">
    <script src="ai-bot/js/chatbot-widget.js" defer></script>
</body>
</html>
```

---

## Provera

1. Otvori sajt u browseru
2. Pogledaj donji desni ugao
3. Trebao bi se pojaviti chatbot ikona
4. Klikni na ikonu - chatbot prozor se otvara

---

## Problemi?

**Chatbot se ne pojavljuje:**
- Proveri da li su putanje ispravne
- Proveri da li su fajlovi upload-ovani
- Otvori Browser Console (F12) i pogledaj za greške

**API ne radi:**
- Proveri da li `api/chatbot.php` postoji
- Proveri da li je OpenAI API key konfigurisan
- Proveri server logove

---

**To je sve! Jednostavno i brzo.** 🚀

