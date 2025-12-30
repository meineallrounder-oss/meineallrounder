# Chatbot Widget - Instalacija na Server

## Problem: Widget se ne prikazuje na serveru

Ako widget ne radi na serveru, proverite sledeće:

### 1. Upload Foldera

**VAŽNO:** Morate uploadovati ceo `verabot` folder na server u isti folder gde je `index.html`.

**Struktura na serveru:**
```
veraconnectgroup.com/
  └── rounder/
      ├── index.html
      ├── styles.css
      ├── script.js
      ├── images/
      └── verabot/          ← OVAJ FOLDER MORA POSTOJATI!
          ├── css/
          │   └── chatbot-widget.css
          ├── js/
          │   └── chatbot-widget.js
          ├── api.php
          ├── config.php
          └── ...
```

### 2. Proverite Putanje u HTML

U `index.html` fajlu, putanje moraju biti relativne:

```html
<!-- U <head> sekciji -->
<link rel="stylesheet" href="verabot/css/chatbot-widget.css">

<!-- Na kraju <body> sekcije -->
<script src="verabot/js/chatbot-widget.js"></script>
```

### 3. Proverite File Permissions

Na serveru, fajlovi moraju imati prava za čitanje:

```bash
chmod 644 verabot/css/chatbot-widget.css
chmod 644 verabot/js/chatbot-widget.js
chmod 644 verabot/api.php
chmod 644 verabot/config.php
```

### 4. Proverite da li Fajlovi Postoje

Otvorite u browser-u:
- `https://veraconnectgroup.com/rounder/verabot/css/chatbot-widget.css`
- `https://veraconnectgroup.com/rounder/verabot/js/chatbot-widget.js`

Ako dobijete 404 ili 500 grešku, fajlovi nisu uploadovani ili su na pogrešnoj lokaciji.

### 5. Proverite Browser Konzolu

Otvorite Developer Tools (F12) i proverite:
- Da li postoje greške u Console tab-u
- Da li se CSS i JS fajlovi učitavaju u Network tab-u
- Da li postoje 404 ili 500 greške

### 6. Test API Endpoint

Otvorite u browser-u:
- `https://veraconnectgroup.com/rounder/verabot/api.php`

Trebalo bi da vidi JSON odgovor (može biti greška ako nema POST zahteva, ali ne bi trebalo da bude 404 ili 500).

## Rešenje

1. **Upload-ujte ceo `verabot` folder** na server u `rounder/` folder
2. **Proverite da li su svi fajlovi uploadovani**
3. **Proverite file permissions** (644 za fajlove, 755 za foldere)
4. **Osvježite stranicu** i proverite konzolu

## Debug

Dodajte ovo u `index.html` pre `</body>` tag-a za debug:

```html
<script>
console.log('Widget CSS path:', document.querySelector('link[href*="chatbot-widget.css"]')?.href);
console.log('Widget JS path:', document.querySelector('script[src*="chatbot-widget.js"]')?.src);
</script>
```

