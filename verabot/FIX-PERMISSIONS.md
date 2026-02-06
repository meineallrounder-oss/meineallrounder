# Fix Permissions na Serveru

## Problem

Fajlovi imaju permissions `0640` što može sprečiti server da ih čita.

## Rešenje

Na serveru, u `verabot` folderu, pokrenite:

```bash
# Postavite permissions za fajlove (readable by all)
find verabot -type f -exec chmod 644 {} \;

# Postavite permissions za foldere (executable by all)
find verabot -type d -exec chmod 755 {} \;

# Ili ručno:
chmod 644 verabot/css/chatbot-widget.css
chmod 644 verabot/js/chatbot-widget.js
chmod 644 verabot/api.php
chmod 644 verabot/config.php
chmod 755 verabot/css/
chmod 755 verabot/js/
chmod 755 verabot/
```

## Provera

Nakon postavljanja permissions, proverite da li fajlovi mogu da se učitaju:

1. Otvorite u browser-u:
   - `https://veraconnectgroup.com/rounder/verabot/css/chatbot-widget.css`
   - `https://veraconnectgroup.com/rounder/verabot/js/chatbot-widget.js`

2. Ako vidite CSS/JS kod, permissions su ispravni.

3. Ako dobijete 403 Forbidden, permissions su još uvek pogrešni.

4. Ako dobijete 404 Not Found, fajlovi nisu na pravoj lokaciji.

## Alternativa: Kroz FTP/SFTP

Ako imate pristup FTP/SFTP klijentu:
1. Desni klik na `verabot` folder → Properties/Permissions
2. Postavite:
   - Folders: 755
   - Files: 644
3. Rekurzivno primenite na sve podfoldere



