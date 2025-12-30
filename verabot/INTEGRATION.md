# Chatbot Widget - Jednostavna integracija

## Kako koristiti chatbot widget na bilo kom sajtu

### Minimalna integracija (samo 2 linije koda!)

Dodajte ove dve linije u vaš HTML fajl:

```html
<!-- U <head> sekciju dodajte CSS -->
<link rel="stylesheet" href="sistem bua ai bot/css/chatbot-widget.css">

<!-- Pre zatvaranja </body> taga dodajte JavaScript -->
<script src="verabot/js/chatbot-widget.js"></script>
```

**To je sve!** Widget će automatski kreirati potrebnu HTML strukturu.

### Primer kompletnog HTML-a:

```html
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moj Sajt</title>
    
    <!-- Chatbot Widget CSS -->
    <link rel="stylesheet" href="sistem bua ai bot/css/chatbot-widget.css">
</head>
<body>
    <h1>Dobrodošli na moj sajt</h1>
    <!-- Vaš sadržaj ovde -->
    
    <!-- Chatbot Widget JavaScript -->
    <script src="verabot/js/chatbot-widget.js"></script>
</body>
</html>
```

### Opciono: Prilagođavanje konfiguracije

Ako želite da prilagodite poruke i naziv kompanije, dodajte opcioni script tag sa konfiguracijom:

```html
<!-- Pre chatbot scripta -->
<script type="application/json" data-chatbot-config>
{
    "companyName": "Moja Kompanija",
    "welcomeMessage": "Dobrodošli! Kako vam mogu pomoći?",
    "toggleText": "Čat Asistent",
    "logoUrl": "images/logo.png"
}
</script>
<script src="verabot/js/chatbot-widget.js"></script>
```

### Folder struktura

```
vaš-sajt/
├── index.html (ili bilo koji HTML fajl)
└── verabot/
    ├── css/
    │   └── chatbot-widget.css
    ├── js/
    │   └── chatbot-widget.js
    ├── api.php
    ├── config.php
    └── ...
```

### Napomene

- Widget automatski detektuje putanju do `api.php` fajla
- Ako već imate HTML strukturu za chatbot u vašem HTML-u, widget će koristiti postojeću
- CSS i JS linkovi mogu biti sa relativnom ili absolutnom putanjom
- Widget je potpuno standalone - ne zahteva dodatne biblioteke

### Podrška za različite putanje

Widget automatski pronalazi `api.php` na osnovu lokacije JavaScript fajla:
- Ako je `chatbot-widget.js` u `verabot/js/`, widget će tražiti `verabot/api.php`
- Radi sa bilo kojom dubinom folder strukture
- Podržava i relativne i absolutne putanje

