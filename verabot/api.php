<?php
/**
 * AI Chatbot API
 * Standalone chatbot system for any website
 * 
 * CONFIGURATION:
 * 1. Create .env file with: OPENAI_API_KEY=your_key_here
 * 2. Customize config.php with your company information
 * 3. Upload to your server
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load configuration
$config = require __DIR__ . '/config.php';

// Load OpenAI API Key - Priority order:
// 1. Vercel/Server Environment Variable (highest priority for production)
// 2. config.php chatbot_settings (if set in admin panel)
// 3. .env files (for local development)
$OPENAI_API_KEY = '';
$api_key_source = 'none';

// FIRST: Check environment variable (Vercel uses this!)
// This is the highest priority for Vercel deployments
$env_key = getenv('OPENAI_API_KEY');
if (!empty($env_key)) {
    $OPENAI_API_KEY = trim($env_key);
    $api_key_source = 'environment variable (Vercel/Server)';
}

// SECOND: Check chatbot settings for API key (if set in admin panel)
if (empty($OPENAI_API_KEY) && isset($config['chatbot_settings']['openai_api_key']) && !empty($config['chatbot_settings']['openai_api_key'])) {
    $OPENAI_API_KEY = trim($config['chatbot_settings']['openai_api_key']);
    $api_key_source = 'config.php';
}

// THIRD: Try .env files (for local development)
if (empty($OPENAI_API_KEY)) {
    $env_paths = [
        __DIR__ . '/.env',
        __DIR__ . '/env',  // Also check 'env' without dot
        __DIR__ . '/../.env',
        $_SERVER['DOCUMENT_ROOT'] . '/../.env',
    ];

    foreach ($env_paths as $env_path) {
        if (file_exists($env_path)) {
            $env_file = file_get_contents($env_path);
            preg_match('/OPENAI_API_KEY\s*=\s*(.+)/', $env_file, $matches);
            if (!empty($matches[1])) {
                $OPENAI_API_KEY = trim($matches[1]);
                $api_key_source = basename($env_path);
                break;
            }
        }
    }
}

// Check if API key is set
if (empty($OPENAI_API_KEY)) {
    http_response_code(500);
    
    // Debug info for troubleshooting
    $debug_info = [
        'api_key_source' => $api_key_source,
        'config_key_exists' => isset($config['chatbot_settings']['openai_api_key']),
        'config_key_empty' => empty($config['chatbot_settings']['openai_api_key'] ?? ''),
        'env_var_exists' => !empty(getenv('OPENAI_API_KEY')),
        'env_files_checked' => [],
        'php_version' => phpversion(),
        'server_info' => [
            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
            'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'N/A',
        ]
    ];
    
    // Check which env files exist
    $env_paths = [
        __DIR__ . '/.env',
        __DIR__ . '/env',
        __DIR__ . '/../.env',
    ];
    
    foreach ($env_paths as $env_path) {
        $debug_info['env_files_checked'][] = [
            'path' => $env_path,
            'exists' => file_exists($env_path),
            'readable' => file_exists($env_path) ? is_readable($env_path) : false
        ];
    }
    
    echo json_encode([
        'error' => 'API Key not configured',
        'response' => 'Entschuldigung, der Chatbot ist momentan nicht verfügbar. Bitte kontaktieren Sie uns unter ' . $config['contact']['email'],
        'debug' => $debug_info,
        'help' => 'Please check: 1) Vercel Environment Variables has OPENAI_API_KEY set, 2) Redeploy after adding the key'
    ]);
    exit();
}

// Helper function to detect language
function detectLanguage($text) {
    $text = mb_strtolower($text);
    if (preg_match('/\b(kako|zdravo|ćao|hvala|molim|da|ne)\b/ui', $text)) {
        return 'Serbian';
    } elseif (preg_match('/\b(hello|hi|thanks|please|yes|no)\b/ui', $text)) {
        return 'English';
    } elseif (preg_match('/\b(hallo|guten|tag|danke|bitte|ja|nein)\b/ui', $text)) {
        return 'German';
    }
    return 'Unknown';
}

// Get user message
$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'] ?? '';

if (empty($user_message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Nachricht ist erforderlich']);
    exit();
}

// Create detailed services list
$services_detailed = [
    'Badsanierung' => 'Komplette Renovierung und Modernisierung von Badezimmern – Alles aus einer Hand. Von der Planung bis zur Schlüsselübergabe.',
    'Hausmeisterservice' => 'Zuverlässige Betreuung von Gebäuden, Reparaturen, Pflege und Instandhaltung. Rundum-Service für Ihre Immobilie.',
    'Trockenbau & Gipsarbeiten' => 'Professionelle Gips- und Trockenbauarbeiten für Decken, Wände und individuelle Raumgestaltung. Präzise Ausführung.',
    'Fliesenlegen & Keramikmontage' => 'Präzise Verlegung von Fliesen und keramischen Elementen für Bad, Küche und Böden. Hochwertige Materialien.',
    'Renovierung von Häusern & Wohnungen' => 'Komplette Reparatur-, Sanierungs- und Modernisierungsarbeiten nach Wunsch. Von der Einzelmaßnahme bis zur Komplettrenovierung.',
    'Beratung & Planung' => 'Individuelle Beratung und maßgeschneiderte Lösungen für Ihr Projekt. Kostenlose Erstberatung vor Ort.'
];

$services_list = implode("\n", array_map(function($service) use ($services_detailed) {
    $description = $services_detailed[$service] ?? '';
    return "• $service: $description";
}, $config['services']));

$values_list = implode("\n", array_map(function($key, $value) {
    return "• $key - $value";
}, array_keys($config['work_method']), $config['work_method']));

$system_message = "Du bist ein EXTREM freundlicher, professioneller und hilfsbereiter KI-Chatbot-Assistent für {$config['company_name']}.

KRITISCHE REGELN - DU MUSST DIESE IMMER BEFOLGEN:

1. SPRACHE - NUR DEUTSCH:
   - Antworte IMMER NUR auf DEUTSCH, egal in welcher Sprache jemand fragt!
   - Wenn jemand auf einer anderen Sprache schreibt, antworte höflich auf Deutsch: \"Gerne helfe ich Ihnen auf Deutsch weiter. Wie kann ich Ihnen helfen?\"
   - Sei IMMER höflich, warmherzig und professionell - NICHT wie ein Roboter!

2. {$config['company_name']} & UNSERE DIENSTLEISTUNGEN - IMMER IM VORDERGRUND:
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
• Name: {$config['company_name']}
• Website: {$config['website']}
• Standort: {$config['location']}
• Adresse: {$config['address']}
• E-Mail: {$config['contact']['email']}
• Telefon: {$config['contact']['phone']}
• Erfahrung: {$config['experience']}
• Team: {$config['team']}

UNSERE DIENSTLEISTUNGEN (DETAILLIERT):
$services_list

UNSERE WERTE:
$values_list

WARUM {$config['company_name']}?
• {$config['experience']}
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
- IMMER {$config['company_name']} Dienstleistungen in den Vordergrund stellen!
- IMMER strukturierte Listen verwenden - NIE Fließtext bei Dienstleistungen!
- Erwähne immer unsere Werte: Qualität, Sauberkeit, Termingerechtigkeit!";

// Prepare OpenAI API request
$data = [
    'model' => 'gpt-3.5-turbo', // Changed from gpt-4o to gpt-3.5-turbo for better compatibility
    'messages' => [
        [
            'role' => 'system',
            'content' => $system_message
        ],
        [
            'role' => 'user',
            'content' => $user_message
        ]
    ],
    'max_tokens' => 400,
    'temperature' => 0.8
];

// Call OpenAI API
$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $OPENAI_API_KEY
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_USERAGENT, 'MeineAllrounder-Chatbot/1.0');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Handle errors
if ($curl_error) {
    http_response_code(500);
    // Log error for debugging
    error_log("Chatbot API curl error: " . $curl_error);
    echo json_encode([
        'error' => 'Connection error: ' . $curl_error,
        'response' => 'Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.',
        'debug' => 'Curl error occurred'
    ]);
    exit();
}

if ($http_code !== 200) {
    http_response_code($http_code);
    $error_data = json_decode($response, true);
    $error_message = $error_data['error']['message'] ?? 'Unknown error';
    $error_type = $error_data['error']['type'] ?? 'unknown';
    
    // Log the error for debugging
    $log_dir = __DIR__ . '/chat-logs';
    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }
    if (is_dir($log_dir)) {
        $error_log = $log_dir . '/api-errors-' . date('Y-m-d') . '.log';
        $error_entry = date('Y-m-d H:i:s') . " - HTTP $http_code - Type: $error_type - Message: $error_message\n";
        @file_put_contents($error_log, $error_entry, FILE_APPEND);
    }
    
    // Also log to PHP error log
    error_log("Chatbot API error - HTTP $http_code - Type: $error_type - Message: $error_message");
    
    // Provide more helpful error message
    $user_message = 'Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.';
    if ($error_type === 'invalid_api_key' || strpos(strtolower($error_message), 'api key') !== false || strpos(strtolower($error_message), 'authentication') !== false) {
        $user_message = 'Entschuldigung, der API-Schlüssel ist ungültig. Bitte kontaktieren Sie uns unter ' . $config['contact']['email'];
    } elseif ($error_type === 'insufficient_quota' || strpos(strtolower($error_message), 'quota') !== false || strpos(strtolower($error_message), 'billing') !== false) {
        $user_message = 'Entschuldigung, das API-Kontingent ist aufgebraucht. Bitte kontaktieren Sie uns unter ' . $config['contact']['email'];
    } elseif ($error_type === 'rate_limit_exceeded' || strpos(strtolower($error_message), 'rate limit') !== false) {
        $user_message = 'Entschuldigung, zu viele Anfragen. Bitte versuchen Sie es in ein paar Momenten erneut.';
    }
    
    echo json_encode([
        'error' => 'OpenAI API error: ' . $error_message,
        'error_type' => $error_type,
        'response' => $user_message,
        'http_code' => $http_code
    ]);
    exit();
}

// Parse response
$response_data = json_decode($response, true);

if (!isset($response_data['choices'][0]['message']['content'])) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Invalid response from OpenAI',
        'response' => 'Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.'
    ]);
    exit();
}

$ai_response = trim($response_data['choices'][0]['message']['content']);

// Log conversation to file
$log_dir = __DIR__ . '/chat-logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

$log_entry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'date' => date('Y-m-d'),
    'time' => date('H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
    'user_message' => $user_message,
    'ai_response' => $ai_response,
    'language' => detectLanguage($user_message)
];

// Save to daily log file
$log_file = $log_dir . '/chat-' . date('Y-m-d') . '.json';
$logs = [];
if (file_exists($log_file)) {
    $logs = json_decode(file_get_contents($log_file), true) ?: [];
}
$logs[] = $log_entry;
file_put_contents($log_file, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Also save to master log (all conversations)
$master_log = $log_dir . '/all-conversations.json';
$master_logs = [];
if (file_exists($master_log)) {
    $master_logs = json_decode(file_get_contents($master_log), true) ?: [];
}
$master_logs[] = $log_entry;
// Keep only last 1000 entries in master log
if (count($master_logs) > 1000) {
    $master_logs = array_slice($master_logs, -1000);
}
file_put_contents($master_log, json_encode($master_logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Return success response
echo json_encode([
    'response' => $ai_response,
    'status' => 'success',
    'api_key_source' => $api_key_source // Debug info - remove in production if needed
]);

