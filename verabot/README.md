# 🤖 AI Chatbot Widget - Standalone System

Profesionalni AI chatbot sistem koji može da se integriše u bilo koji sajt.

## 📁 Struktura Fajlova

```
chat/
├── api.php                 # Chatbot API endpoint
├── config.php              # Konfiguracija kompanije (CUSTOMIZE THIS!)
├── chat-admin.php          # Admin panel za pregled logova
├── widget.html             # Primer integracije
├── .env.example            # Primer .env fajla
├── .htaccess               # Zaštita fajlova
├── README.md               # Ova dokumentacija
├── js/
│   └── chatbot-widget.js  # JavaScript za widget
├── css/
│   └── chatbot-widget.css # CSS stilovi
└── chat-logs/             # Folder za logove (kreira se automatski)
    └── .htaccess          # Zaštita logova
```

## 🚀 Brza Instalacija

### 1. Upload fajlova
Uploaduj ceo `chat/` folder na svoj server.

### 2. Konfiguracija API Key
```bash
# Kopiraj .env.example u .env
cp .env.example .env

# Edituj .env i dodaj svoj OpenAI API key
OPENAI_API_KEY=sk-tvoj-api-key-ovde
```

### 3. Konfiguracija Kompanije
Edituj `config.php` i dodaj informacije o svojoj kompaniji:
- Company name
- Services
- Contact information
- About, team, specialization

### 4. Integracija Widget-a
Dodaj u svoje HTML stranice:

```html
<!-- CSS -->
<link rel="stylesheet" href="chat/css/chatbot-widget.css">

<!-- Widget HTML (pre </body>) -->
<div id="chatbot-widget">
    <!-- Kopiraj HTML iz widget.html -->
</div>

<!-- JavaScript -->
<script src="chat/js/chatbot-widget.js"></script>
```

## ⚙️ Konfiguracija

### config.php
Edituj sa informacijama o svojoj kompaniji:
- `company_name` - Ime kompanije
- `services` - Lista usluga
- `contact` - Email i telefon
- `about`, `team`, `specialization` - Opisi

### .env
Dodaj svoj OpenAI API key:
```
OPENAI_API_KEY=sk-tvoj-api-key
```

### chat-admin.php
Promeni password (linija 14):
```php
$admin_password = 'tvoja_jaka_sifra';
```

## 🔐 Admin Panel

Pristup logovima:
- URL: `https://yourwebsite.com/chat/chat-admin.php`
- Password: (ono što si postavio u chat-admin.php)

## 📝 Funkcionalnosti

✅ **Multilingual Support** - Automatska detekcija jezika (Deutsch, English, Serbian, etc.)
✅ **Conversation Logging** - Sve konverzacije se loguju
✅ **Admin Panel** - Pregled svih konverzacija
✅ **Responsive Design** - Radi na svim uređajima
✅ **Customizable** - Lako prilagodljiv tvojoj kompaniji

## 🔧 API Endpoint

Widget automatski pronalazi API endpoint:
- Ako je widget u `chat/` folderu: `chat/api.php`
- Ako je widget u root-u: `chat/api.php`
- Dinamički određuje putanju na osnovu lokacije

## 📊 Logovi

Logovi se čuvaju u `chat-logs/` folderu:
- Dnevni logovi: `chat-YYYY-MM-DD.json`
- Master log: `all-conversations.json` (poslednjih 1000)

## 🛡️ Bezbednost

- `.env` fajl je zaštićen `.htaccess`
- `chat-logs/` folder je zaštićen
- Admin panel zaštićen password-om
- Session timeout: 30 minuta

## 📞 Podrška

Za pitanja i podršku, kontaktiraj developera.

---

**Napomena:** Ne zaboravi da promeniš:
1. ✅ Password u `chat-admin.php`
2. ✅ Informacije u `config.php`
3. ✅ OpenAI API key u `.env`

