# Chatbot Troubleshooting Guide

## 🔧 Problem: Chatbot se ne pojavljuje na sajtu

### Provera:

1. **Proveri da li su fajlovi na pravim putanjama:**
   - `verabot/css/chatbot-widget.css` - postoji?
   - `verabot/js/chatbot-widget.js` - postoji?
   - `verabot/api.php` - postoji?

2. **Proveri browser console (F12):**
   - Otvori Developer Tools (F12)
   - Idi na "Console" tab
   - Traži greške (crveni tekst)

3. **Proveri Network tab:**
   - Otvori Developer Tools (F12)
   - Idi na "Network" tab
   - Osvježi stranicu
   - Traži `chatbot-widget.js`, `chatbot-widget.css`, `api.php`
   - Proveri da li su svi 200 OK

---

## 🐛 Česti Problemi

### Problem 1: "Chatbot elements not found"

**Razlog:** JavaScript se izvršava pre nego što je HTML kreiran.

**Rešenje:** 
- Proveri da li je `chatbot-widget.js` na kraju `<body>` taga
- Proveri da li postoji `document.body` kada se skripta izvršava

### Problem 2: API Error ili 404

**Razlog:** `api.php` ne može da se pronađe.

**Rešenje:**
- Proveri da li `verabot/api.php` postoji
- Proveri putanje u browser Network tabu
- Proveri da li server podržava PHP

### Problem 3: Chatbot se ne vidi (CSS problem)

**Razlog:** CSS fajl se ne učitava ili ima grešku.

**Rešenje:**
- Proveri da li je `chatbot-widget.css` linkovan u `<head>`
- Proveri Network tab da li je CSS učitavan
- Proveri browser console za CSS greške

### Problem 4: Settings se ne učitavaju

**Razlog:** `api-settings.php` ne postoji ili ne vraća podatke.

**Rešenje:**
- Chatbot će raditi sa default podešavanjima
- Ovo nije kritično - chatbot će se pojaviti sa default bojama

---

## ✅ Brzi Fix

Ako chatbot ne radi uopšte, proveri:

1. **Browser Console** - ima li grešaka?
2. **Network Tab** - učitavaju li se fajlovi?
3. **JavaScript Enabled?** - Da li je JavaScript omogućen u browseru?
4. **File Paths** - Da li su putanje ispravne?

---

## 🔍 Debug Mode

Dodaj u `chatbot-widget.js` na početak:

```javascript
console.log('Chatbot widget script loaded');
console.log('API URL:', API_URL);
```

To će ti pomoći da vidiš šta se dešava.

---

**Ako problem persista, proveri browser console za detalje!**






