# 📤 Instrukcije za Upload Widget-a na Server

## 🎯 Gde Treba da Bude `verabot` Folder

Na serveru, struktura **MORA** biti ovako:

```
veraconnectgroup.com/
  └── rounder/                    ← Glavni folder sajta
      ├── index.html              ← Glavna stranica
      ├── styles.css
      ├── script.js
      ├── images/
      │   ├── logo.png
      │   └── ...
      └── verabot/                ← OVAJ FOLDER MORA BITI OVDE!
          ├── css/
          │   └── chatbot-widget.css
          ├── js/
          │   └── chatbot-widget.js
          ├── api.php
          ├── config.php
          ├── .htaccess
          ├── env
          └── ... (ostali fajlovi)
```

## ✅ Koraci za Upload

### 1. Povezivanje na Server

Povežite se na server preko FTP/SFTP klijenta (FileZilla, Cyberduck, itd.)

### 2. Navigacija do Foldera

Idite u folder: `veraconnectgroup.com/rounder/`

### 3. Upload `verabot` Foldera

**VAŽNO:** Upload-ujte **CEO** `verabot` folder (sa svim podfolderima i fajlovima) u `rounder/` folder.

**Struktura nakon upload-a:**
```
rounder/
  ├── index.html
  ├── styles.css
  ├── script.js
  ├── images/
  └── verabot/          ← OVDE!
      ├── css/
      ├── js/
      ├── api.php
      └── ...
```

### 4. Provera Putanja

U `index.html` fajlu, putanje su:
```html
<link rel="stylesheet" href="verabot/css/chatbot-widget.css">
<script src="verabot/js/chatbot-widget.js"></script>
```

To znači da `verabot` folder **MORA** biti u istom folderu kao `index.html`.

## 🔍 Kako Proveriti da li je Na Pravom Mestu

### Opcija 1: Preko FTP/SFTP

1. Povežite se na server
2. Idite u `rounder/` folder
3. Proverite da li vidite:
   - `index.html`
   - `styles.css`
   - `script.js`
   - `images/` folder
   - `verabot/` folder ← OVO TREBA DA POSTOJI!

### Opcija 2: Preko Browser-a

Otvorite u browser-u:
- `https://veraconnectgroup.com/rounder/verabot/css/chatbot-widget.css`

Ako vidite CSS kod → folder je na pravom mestu ✅
Ako dobijete 404 → folder nije na pravom mestu ❌

## ⚠️ Česte Greške

### ❌ POGREŠNO:
```
rounder/
  └── verabot/          ← NIJE OVDE!
      └── rounder/
          └── index.html
```

### ❌ POGREŠNO:
```
rounder/
  ├── index.html
  └── assets/
      └── verabot/      ← NIJE OVDE!
```

### ✅ ISPRAVNO:
```
rounder/
  ├── index.html
  └── verabot/          ← OVDE!
```

## 🛠️ Ako Folder Nije Na Pravom Mestu

1. **Pronađite gde je trenutno:**
   - Pretražite server za `chatbot-widget.js`
   - Ili proverite FTP strukturu

2. **Premestite ga:**
   - Preko FTP: Drag & drop `verabot` folder u `rounder/` folder
   - Preko SSH: `mv /path/to/wrong/verabot /path/to/rounder/verabot`

3. **Proverite ponovo:**
   - Otvorite `https://veraconnectgroup.com/rounder/verabot/css/chatbot-widget.css`
   - Trebalo bi da vidite CSS kod

## 📝 Checklist

- [ ] `verabot` folder je u `rounder/` folderu
- [ ] `verabot/css/chatbot-widget.css` postoji
- [ ] `verabot/js/chatbot-widget.js` postoji
- [ ] `verabot/api.php` postoji
- [ ] `verabot/config.php` postoji
- [ ] Permissions su 644 za fajlove, 755 za foldere
- [ ] `.htaccess` je uploadovan
- [ ] Test: `https://veraconnectgroup.com/rounder/verabot/css/chatbot-widget.css` radi

## 🎯 Finalna Provera

Nakon upload-a, otvorite:
1. `https://veraconnectgroup.com/rounder/index.html`
2. Otvorite Developer Tools (F12)
3. Proverite Console tab - trebalo bi da vidite:
   - `Chatbot widget script loaded`
   - `Chatbot API URL: ...`
   - `Widget created: true`

Ako vidite ove poruke, widget je uspešno instaliran! ✅



