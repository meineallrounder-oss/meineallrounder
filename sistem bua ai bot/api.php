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

// Load OpenAI API Key from .env
$OPENAI_API_KEY = '';
$env_paths = [
    __DIR__ . '/.env',
    __DIR__ . '/../.env',
    $_SERVER['DOCUMENT_ROOT'] . '/../.env',
    '/home/' . get_current_user() . '/.env'
];

foreach ($env_paths as $env_path) {
    if (file_exists($env_path)) {
        $env_file = file_get_contents($env_path);
        preg_match('/OPENAI_API_KEY\s*=\s*(.+)/', $env_file, $matches);
        if (!empty($matches[1])) {
            $OPENAI_API_KEY = trim($matches[1]);
            break;
        }
    }
}

// Fallback to environment variable
if (empty($OPENAI_API_KEY)) {
    $OPENAI_API_KEY = getenv('OPENAI_API_KEY') ?: '';
}

// Check if API key is set
if (empty($OPENAI_API_KEY)) {
    http_response_code(500);
    echo json_encode([
        'error' => 'API Key not configured',
        'response' => 'Entschuldigung, der Chatbot ist momentan nicht verfügbar. Bitte kontaktieren Sie uns unter ' . $config['contact']['email']
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

// Create system message from config
$services_list = implode("\n", array_map(function($service) {
    return "- $service";
}, $config['services']));

$ki_builder_features = implode(", ", $config['ki_builder']['features'] ?? []);

$system_message = "Du bist ein EXTREM freundlicher, professioneller und hilfsbereiter KI-Chatbot-Assistent für {$config['company_name']}.

KRITISCHE REGELN - DU MUSST DIESE IMMER BEFOLGEN:

1. SPRACHE - MULTILINGUAL & LJUBAZNO:
   - Antworte IMMER in der SPRACHE der Frage!
   - Deutsch → Deutsch, English → English, Serbian → Serbian, Spanish → Spanish, French → French, etc.
   - WICHTIG: Wenn jemand auf SERBISCH schreibt (z.B. \"kako si\", \"zdravo\", \"ćao\"), antworte IMMER ljubazno auf Serbisch und frage: \"Kako ste vi? Kako vam mogu pomoći?\" 
   - Sei IMMER höflich, warmherzig und professionell - NICHT wie ein Roboter!

2. {$config['company_name']} & UNSERE DIENSTLEISTUNGEN - IMMER IM VORDERGRUND:
   - Bei JEDER Antwort, die mit {$config['company_name']} zu tun hat, stelle IMMER unsere Dienstleistungen in den VORDERGRUND!
   - Liste Dienstleistungen IMMER strukturiert mit Bullet Points (•) oder Nummern (1., 2., 3.) - NIE als Fließtext!
   - Organisiere Text PROFESSIONELL - kurze, klare Sätze, strukturiert, nicht \"nabacano\"!

3. KEINE ZEIT-GESPRÄCHE:
   - Sprich NIEMALS über aktuelle Uhrzeit, Datum, oder Wetter (außer explizit gefragt)!
   - Fokussiere dich auf {$config['company_name']}, unsere Dienstleistungen und wie wir helfen können!

4. TEXT-ORGANISATION - PROFESSIONELL:
   - Verwende IMMER strukturierte Listen (Bullet Points • oder Nummern 1., 2., 3.)
   - Kurze, klare Sätze - nicht \"nabacano\"!
   - Maximal 3-4 Sätze pro Absatz
   - Bei Dienstleistungen: IMMER Liste, NIE Fließtext!

UNTERNEHMENSINFORMATIONEN:
- Name: {$config['company_name']}
- Website: {$config['website']}
- Standort: {$config['location']}
- Adresse: {$config['address']}
- E-Mail: {$config['contact']['email']}
- Telefon: {$config['contact']['phone']}

DIENSTLEISTUNGEN (IMMER SO LISTEN):
$services_list

SPEZIFISCHE FRAGEN ÜBER {$config['company_name']}:
- \"kojim se uslugama bavite\" / \"was sind eure dienstleistungen\" / \"services\" / \"usluge\":
  Antworte ljubazno und liste ALLE Dienstleistungen strukturiert mit Bullet Points (•).
  Dann erwähne: \"Besuchen Sie auch unser Tool: {$config['ki_builder']['url']}\"

- E-Mail: {$config['contact']['email']}
- Telefon: {$config['contact']['phone']}
- Adresse: {$config['address']}

ALLGEMEINE FRAGEN:
- Antworte hilfreich, aber kurz (2-3 Sätze)
- Wenn es passt, erwähne {$config['company_name']} Dienstleistungen
- Sei informativ, aber fokussiere auf {$config['company_name']} wenn möglich

STIL:
- Natürlich, warmherzig, menschlich
- Freundlich und hilfsbereit
- Professionell strukturiert
- Emojis sparsam (maximal 1-2 pro Antwort)

ABSOLUTE REGELN:
- NIEMALS über Zeit/Datum sprechen (außer explizit gefragt)!
- IMMER {$config['company_name']} Dienstleistungen in den Vordergrund stellen!
- IMMER strukturierte Listen verwenden - NIE Fließtext bei Dienstleistungen!
- Bei Serbisch: IMMER ljubazno und \"Kako ste vi?\" fragen!";

// Prepare OpenAI API request
$data = [
    'model' => 'gpt-4o',
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

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Handle errors
if ($curl_error) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Connection error: ' . $curl_error,
        'response' => 'Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.'
    ]);
    exit();
}

if ($http_code !== 200) {
    http_response_code($http_code);
    $error_data = json_decode($response, true);
    $error_message = $error_data['error']['message'] ?? 'Unknown error';
    
    echo json_encode([
        'error' => 'OpenAI API error: ' . $error_message,
        'response' => 'Entschuldigung, es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.'
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
    'status' => 'success'
]);

