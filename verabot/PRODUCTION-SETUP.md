# 🚀 Produkcija Setup - AI Chatbot sa Admin Panelom

## ✅ Šta je Urađeno

### 1. **Admin Panel za Podešavanja** (`chatbot-admin.php`)

Kompletno funkcionalan admin panel sa:

✅ **Podešavanje Boja:**
- Universal Farbe (primarna boja)
- Header Farbe (primarna i sekundarna)
- User Nachricht Farbe (poruke korisnika)
- Toggle Button Farbe
- Hintergrund Farbe

✅ **Biranje Ikonice:**
- 12 emoji opcija (🤖, 💬, 🦾, ✨, ⚡, 🎯, 🚀, 💡, 🌟, 🎨, 🔮, ⚙️)
- Klik na emoji da izabereš

✅ **Dodavanje Logoa:**
- URL polje za logo
- Automatski prikaz ako je dostupan

✅ **OpenAI API Key Setup:**
- **MOŽE SE DODATI PREKO ADMIN PANELA!** ✅
- Čuva se i u `.env` i u `config.php`
- Maskirano polje sa opcijom "Anzeigen/Verbergen"
- Status indikator (✅ ako je setovan, ⚠️ ako nije)

---

## 📍 Kako Da Koristiš

### 1. Pristup Admin Panelu

```
URL: http://localhost/verabot/chatbot-admin.php
Password: vera75433
```

**Na live serveru:**
```
https://tvoj-domen.com/verabot/chatbot-admin.php
```

### 2. Dodavanje OpenAI API Key

1. Otvori admin panel
2. Idi na sekciju "🔑 API Einstellungen"
3. Unesi OpenAI API key (format: `sk-...`)
4. Klikni "👁️ Anzeigen" da vidiš trenutni key
5. Klikni "💾 Einstellungen Speichern"

**API key se automatski čuva u:**
- `config.php` (prioritet - koristi se prvo)
- `.env` fajl (backward compatibility)

### 3. Podešavanje Boja

1. Koristi color picker ili unesi hex kod
2. Preview se automatski ažurira
3. Sačuvaj promene

### 4. Dodavanje Logoa

1. Unesi validan URL logoa (http:// ili https://)
2. Logo će se prikazati u header-u chatbota
3. Ako logo nije dostupan, koristi se emoji ikonica

---

## 🔄 Prioritet API Key-a

API key se učitava u sledećem redosledu:

1. **Config.php** (`chatbot_settings['openai_api_key']`) - **Najviši prioritet** ✅
2. `.env` fajl (`OPENAI_API_KEY=`)
3. Environment variable (`getenv('OPENAI_API_KEY')`)

**To znači:** API key setovan preko admin panela ima najveći prioritet!

---

## 📝 Napomene

### Security

⚠️ **Promeni password u produkciji!**

U `chatbot-admin.php` i `chat-admin.php`:
```php
$admin_password = 'vera75433'; // ⚠️ PROMENI OVO!
```

### File Permissions

Proveri da li PHP može da upisuje u:
- `config.php`
- `.env` fajl (ako postoji)

### Backward Compatibility

- Ako API key postoji u `.env`, admin panel će ga prikazati
- Kada se setuje preko admin panela, čuva se i u `.env` i u `config.php`
- `config.php` ima prioritet nad `.env`

---

## 🎯 Funkcionalnosti za Produkciju

✅ Svi podešavanja se čuvaju u `config.php`
✅ Dinamičko primenjivanje bez restartovanja servera
✅ API key može biti setovan preko admin panela
✅ Validacija i prikaz statusa API key-a
✅ Maskirani prikaz API key-a sa opcijom prikaza
✅ Pregled logova chat-a (`chat-admin.php`)
✅ Integracija između Settings i Logs panela

---

## 🚀 Deployment Checklist

- [ ] Promeni password u admin panelima
- [ ] Dodaj OpenAI API key preko admin panela
- [ ] Podesi boje prema brendu
- [ ] Dodaj logo URL (opciono)
- [ ] Izaberi ikonicu
- [ ] Testiraj chat funkcionalnost
- [ ] Proveri file permissions (`config.php`, `.env`)
- [ ] Backup `config.php` pre promena

---

**Sve je spremno za produkciju!** 🎉




