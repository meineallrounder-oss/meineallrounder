<?php
/**
 * Contact Form API Endpoint
 * Handles contact form submissions from the website
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

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['name'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Missing required fields: ' . implode(', ', $missing_fields)
    ]);
    exit();
}

// Sanitize input
$name = htmlspecialchars(trim($input['name'] ?? ''));
$email = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$subject = htmlspecialchars(trim($input['subject'] ?? 'Kontaktanfrage von Website'));
$message = htmlspecialchars(trim($input['message'] ?? ''));
$phone = htmlspecialchars(trim($input['phone'] ?? ''));
$service = htmlspecialchars(trim($input['service'] ?? ''));
$area = htmlspecialchars(trim($input['area'] ?? ''));

// Validate email if provided
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email address']);
    exit();
}

// If no email provided, use a placeholder
if (empty($email)) {
    $email = 'keine-email@meineallrounder.de';
}

// Recipient email (change this to your email)
$to = 'info@meineallrounder.de';

// Determine form type and create email subject
$form_type = isset($input['service']) ? 'Angebotsanfrage' : 'Kontaktanfrage';
$email_subject = $form_type . ' von ' . $name;

// Create email body
$email_body = "Neue $form_type von der Website\n\n";
$email_body .= "Name: $name\n";
$email_body .= "E-Mail: $email\n";

if (!empty($phone)) {
    $email_body .= "Telefon: $phone\n";
}

if (!empty($subject)) {
    $email_body .= "Betreff: $subject\n";
}

if (!empty($service)) {
    $email_body .= "Art der Arbeit: $service\n";
}

if (!empty($area)) {
    $email_body .= "Fläche: $area m²\n";
}

if (!empty($message)) {
    $email_body .= "\nNachricht:\n$message\n";
}

$email_body .= "\n---\n";
$email_body .= "Gesendet am: " . date('d.m.Y H:i:s') . "\n";
$email_body .= "IP-Adresse: " . ($_SERVER['REMOTE_ADDR'] ?? 'unbekannt') . "\n";

// Email headers
$headers = [
    'From: ' . $name . ' <' . $email . '>',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8'
];

// Send email
$mail_sent = mail($to, $email_subject, $email_body, implode("\r\n", $headers));

if ($mail_sent) {
    // Log the submission
    $log_dir = __DIR__ . '/contact-logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'date' => date('Y-m-d'),
        'time' => date('H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'form_type' => $form_type,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
        'service' => $service,
        'area' => $area
    ];
    
    $log_file = $log_dir . '/contact-' . date('Y-m-d') . '.json';
    $logs = [];
    if (file_exists($log_file)) {
        $logs = json_decode(file_get_contents($log_file), true) ?: [];
    }
    $logs[] = $log_entry;
    file_put_contents($log_file, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Ihre Nachricht wurde erfolgreich gesendet. Wir werden uns bald bei Ihnen melden.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to send email',
        'message' => 'Es gab einen Fehler beim Senden Ihrer Nachricht. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns direkt unter info@meineallrounder.de'
    ]);
}

