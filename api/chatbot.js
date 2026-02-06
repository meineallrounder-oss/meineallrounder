/**
 * Vercel Serverless Function for Chatbot API
 * This replaces the PHP endpoint for Vercel deployment
 */

module.exports = async function handler(req, res) {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  // Handle preflight requests
  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  // Only allow POST
  if (req.method !== 'POST') {
    res.status(405).json({ error: 'Method not allowed' });
    return;
  }

  // Get API key from environment variable (Vercel)
  const OPENAI_API_KEY = process.env.OPENAI_API_KEY;

  if (!OPENAI_API_KEY) {
    console.error('OPENAI_API_KEY not found in environment variables');
    res.status(500).json({
      error: 'API Key not configured',
      response: 'Entschuldigung, der Chatbot ist momentan nicht verfügbar. Bitte kontaktieren Sie uns unter info@meineallrounder.de',
      help: 'Please set OPENAI_API_KEY in Vercel Environment Variables',
      debug: {
        env_keys: Object.keys(process.env).filter(k => k.includes('OPENAI') || k.includes('API')),
        has_key: !!process.env.OPENAI_API_KEY
      }
    });
    return;
  }

  // Log API key status (without exposing the key)
  console.log('API Key found:', OPENAI_API_KEY ? OPENAI_API_KEY.substring(0, 7) + '...' + OPENAI_API_KEY.substring(OPENAI_API_KEY.length - 4) : 'NOT FOUND');

  // Get user message
  const { message } = req.body;

  if (!message || !message.trim()) {
    res.status(400).json({ error: 'Nachricht ist erforderlich' });
    return;
  }

  // Company configuration
  const config = {
    company_name: 'Meine Allrounder',
    website: 'meineallrounder.de',
    location: 'Moers, Deutschland',
    address: 'Franzstr. 20, 47441 Moers, Deutschland',
    contact: {
      email: 'info@meineallrounder.de',
      phone: '+49 15211501980'
    },
    services: [
      {
        name: 'Badsanierung',
        description: 'Komplette Renovierung und Modernisierung von Badezimmern – Alles aus einer Hand. Von der Planung bis zur Schlüsselübergabe.'
      },
      {
        name: 'Hausmeisterservice',
        description: 'Zuverlässige Betreuung von Gebäuden, Reparaturen, Pflege und Instandhaltung. Rundum-Service für Ihre Immobilie.'
      },
      {
        name: 'Trockenbau & Gipsarbeiten',
        description: 'Professionelle Gips- und Trockenbauarbeiten für Decken, Wände und individuelle Raumgestaltung. Präzise Ausführung.'
      },
      {
        name: 'Fliesenlegen & Keramikmontage',
        description: 'Präzise Verlegung von Fliesen und keramischen Elementen für Bad, Küche und Böden. Hochwertige Materialien.'
      },
      {
        name: 'Renovierung von Häusern & Wohnungen',
        description: 'Komplette Reparatur-, Sanierungs- und Modernisierungsarbeiten nach Wunsch. Von der Einzelmaßnahme bis zur Komplettrenovierung.'
      },
      {
        name: 'Beratung & Planung',
        description: 'Individuelle Beratung und maßgeschneiderte Lösungen für Ihr Projekt. Kostenlose Erstberatung vor Ort.'
      }
    ],
    values: [
      'Qualität - Größter Wert auf Qualität und Präzision',
      'Sauberkeit - Saubere Arbeitsweise und ordentliche Durchführung',
      'Termingerecht - Zuverlässige Einhaltung von Terminen',
      'Nachhaltigkeit - Nachhaltige Materialien und Arbeitsweisen'
    ],
    experience: 'Mehr als 10 Jahre Erfahrung im Handwerk',
    team: 'Erfahrene Fachkräfte unter der Leitung von Rajko Durdevic (CEO)'
  };

  // Create system message
  const services_list = config.services.map(s => `• ${s.name}: ${s.description}`).join('\n');
  const values_list = config.values.map(v => `• ${v}`).join('\n');
  
  const system_message = `Du bist ein EXTREM freundlicher, professioneller und hilfsbereiter KI-Chatbot-Assistent für ${config.company_name}.

KRITISCHE REGELN - DU MUSST DIESE IMMER BEFOLGEN:

1. SPRACHE - NUR DEUTSCH:
   - Antworte IMMER NUR auf DEUTSCH, egal in welcher Sprache jemand fragt!
   - Wenn jemand auf einer anderen Sprache schreibt, antworte höflich auf Deutsch: "Gerne helfe ich Ihnen auf Deutsch weiter. Wie kann ich Ihnen helfen?"
   - Sei IMMER höflich, warmherzig und professionell - NICHT wie ein Roboter!

2. ${config.company_name} & UNSERE DIENSTLEISTUNGEN - IMMER IM VORDERGRUND:
   - Bei JEDER Antwort stelle IMMER unsere Dienstleistungen und unser Unternehmen in den VORDERGRUND!
   - Liste Dienstleistungen IMMER strukturiert mit Bullet Points (•) oder Nummern (1., 2., 3.) - NIE als Fließtext!
   - Erwähne unsere Werte: Qualität, Sauberkeit, Termingerechtigkeit und Nachhaltigkeit!

3. KEINE ZEIT-GESPRÄCHE:
   - Sprich NIEMALS über aktuelle Uhrzeit, Datum, oder Wetter (außer explizit gefragt)!

4. TEXT-ORGANISATION - PROFESSIONELL:
   - Verwende IMMER strukturierte Listen (Bullet Points • oder Nummern 1., 2., 3.)
   - Kurze, klare Sätze
   - Bei Dienstleistungen: IMMER Liste, NIE Fließtext!

UNTERNEHMENSINFORMATIONEN:
• Name: ${config.company_name}
• Website: ${config.website}
• Standort: ${config.location}
• Adresse: ${config.address}
• E-Mail: ${config.contact.email}
• Telefon: ${config.contact.phone}
• Erfahrung: ${config.experience}
• Team: ${config.team}

UNSERE DIENSTLEISTUNGEN (DETAILLIERT):
${services_list}

UNSERE WERTE:
${values_list}

WARUM ${config.company_name}?
• ${config.experience}
• Professionelle Ausführung mit hochwertigen Materialien
• Transparente Preise und detaillierte Angebote
• Kostenlose Erstberatung vor Ort
• Garantie auf alle Arbeiten
• Langfristiger Service und Betreuung

STIL:
- Natürlich, warmherzig, menschlich
- Freundlich und hilfsbereit
- Professionell strukturiert
- Emojis sparsam (maximal 1-2 pro Antwort)
- Immer auf Deutsch antworten!

ABSOLUTE REGELN:
- NIEMALS über Zeit/Datum sprechen (außer explizit gefragt)!
- IMMER auf DEUTSCH antworten, egal welche Sprache der Nutzer verwendet!
- IMMER ${config.company_name} Dienstleistungen in den Vordergrund stellen!
- IMMER strukturierte Listen verwenden - NIE Fließtext bei Dienstleistungen!
- Erwähne immer unsere Werte: Qualität, Sauberkeit, Termingerechtigkeit!`;

  // Prepare OpenAI API request
  const data = {
    model: 'gpt-3.5-turbo',
    messages: [
      {
        role: 'system',
        content: system_message
      },
      {
        role: 'user',
        content: message
      }
    ],
    max_tokens: 400,
    temperature: 0.8
  };

  try {
    // Call OpenAI API
    const response = await fetch('https://api.openai.com/v1/chat/completions', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${OPENAI_API_KEY}`
      },
      body: JSON.stringify(data)
    });

    if (!response.ok) {
      const errorData = await response.json();
      const errorMessage = errorData.error?.message || 'Unknown error';
      const errorType = errorData.error?.type || 'unknown';

      let userMessage = 'Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.';
      
      if (errorType === 'invalid_api_key' || errorMessage.toLowerCase().includes('api key') || errorMessage.toLowerCase().includes('authentication')) {
        userMessage = 'Entschuldigung, der API-Schlüssel ist ungültig. Bitte kontaktieren Sie uns unter ' + config.contact.email;
      } else if (errorType === 'insufficient_quota' || errorMessage.toLowerCase().includes('quota') || errorMessage.toLowerCase().includes('billing')) {
        userMessage = 'Entschuldigung, das API-Kontingent ist aufgebraucht. Bitte kontaktieren Sie uns unter ' + config.contact.email;
      } else if (errorType === 'rate_limit_exceeded' || errorMessage.toLowerCase().includes('rate limit')) {
        userMessage = 'Entschuldigung, zu viele Anfragen. Bitte versuchen Sie es in ein paar Momenten erneut.';
      }

      res.status(response.status).json({
        error: 'OpenAI API error: ' + errorMessage,
        error_type: errorType,
        response: userMessage
      });
      return;
    }

    const responseData = await response.json();

    if (!responseData.choices || !responseData.choices[0] || !responseData.choices[0].message) {
      res.status(500).json({
        error: 'Invalid response from OpenAI',
        response: 'Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.'
      });
      return;
    }

    const botResponse = responseData.choices[0].message.content;

    res.status(200).json({
      response: botResponse,
      status: 'success'
    });

  } catch (error) {
    console.error('Chatbot API error:', error);
    res.status(500).json({
      error: 'Connection error: ' + error.message,
      response: 'Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.'
    });
  }
}
