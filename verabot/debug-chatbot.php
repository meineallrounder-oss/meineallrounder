<?php
/**
 * Debug endpoint for chatbot
 * Check if API key is loaded and test connection
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$config = require __DIR__ . '/config.php';

// Load OpenAI API Key - same logic as api.php
$OPENAI_API_KEY = '';
$api_key_source = 'none';

// Check environment variable first (Vercel)
$env_key = getenv('OPENAI_API_KEY');
if (!empty($env_key)) {
    $OPENAI_API_KEY = trim($env_key);
    $api_key_source = 'environment variable (Vercel/Server)';
}

// Check config.php
if (empty($OPENAI_API_KEY) && isset($config['chatbot_settings']['openai_api_key']) && !empty($config['chatbot_settings']['openai_api_key'])) {
    $OPENAI_API_KEY = trim($config['chatbot_settings']['openai_api_key']);
    $api_key_source = 'config.php';
}

// Check .env files
if (empty($OPENAI_API_KEY)) {
    $env_paths = [
        __DIR__ . '/.env',
        __DIR__ . '/env',
        __DIR__ . '/../.env',
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

$debug_info = [
    'api_key_found' => !empty($OPENAI_API_KEY),
    'api_key_source' => $api_key_source,
    'api_key_length' => !empty($OPENAI_API_KEY) ? strlen($OPENAI_API_KEY) : 0,
    'api_key_preview' => !empty($OPENAI_API_KEY) ? substr($OPENAI_API_KEY, 0, 7) . '...' . substr($OPENAI_API_KEY, -4) : 'N/A',
    'php_version' => phpversion(),
    'curl_available' => function_exists('curl_init'),
    'server_env' => [
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'N/A',
    ],
    'env_files_checked' => []
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

// Test API key if found
if (!empty($OPENAI_API_KEY)) {
    $test_data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'user',
                'content' => 'Say "Hello" if you can read this.'
            ]
        ],
        'max_tokens' => 10
    ];
    
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $OPENAI_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    $debug_info['api_test'] = [
        'http_code' => $http_code,
        'curl_error' => $curl_error ?: 'none',
        'response_received' => !empty($response),
        'response_length' => strlen($response ?? ''),
    ];
    
    if ($http_code === 200) {
        $response_data = json_decode($response, true);
        $debug_info['api_test']['success'] = true;
        $debug_info['api_test']['response_preview'] = isset($response_data['choices'][0]['message']['content']) 
            ? substr($response_data['choices'][0]['message']['content'], 0, 50) 
            : 'Invalid response format';
    } else {
        $error_data = json_decode($response, true);
        $debug_info['api_test']['success'] = false;
        $debug_info['api_test']['error'] = $error_data['error']['message'] ?? 'Unknown error';
        $debug_info['api_test']['error_type'] = $error_data['error']['type'] ?? 'unknown';
    }
} else {
    $debug_info['api_test'] = [
        'success' => false,
        'error' => 'No API key found to test'
    ];
}

echo json_encode($debug_info, JSON_PRETTY_PRINT);
