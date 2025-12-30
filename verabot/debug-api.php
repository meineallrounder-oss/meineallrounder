<?php
/**
 * Debug API Endpoint
 * Use this to test if API key is being read correctly
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Load configuration
$config = require __DIR__ . '/config.php';

// Load OpenAI API Key - Check config.php FIRST (highest priority)
$OPENAI_API_KEY = '';

// Check chatbot settings for API key (if set in admin panel) - Highest priority
if (isset($config['chatbot_settings']['openai_api_key']) && !empty($config['chatbot_settings']['openai_api_key'])) {
    $OPENAI_API_KEY = trim($config['chatbot_settings']['openai_api_key']);
}

// If not in config, try .env files
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
                break;
            }
        }
    }
}

// Debug information
$debug_info = [
    'config_key_exists' => isset($config['chatbot_settings']['openai_api_key']),
    'config_key_empty' => empty($config['chatbot_settings']['openai_api_key'] ?? ''),
    'config_key_length' => strlen($config['chatbot_settings']['openai_api_key'] ?? ''),
    'api_key_found' => !empty($OPENAI_API_KEY),
    'api_key_length' => strlen($OPENAI_API_KEY),
    'api_key_preview' => !empty($OPENAI_API_KEY) ? substr($OPENAI_API_KEY, 0, 10) . '...' . substr($OPENAI_API_KEY, -10) : 'N/A',
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
        'exists' => file_exists($env_path)
    ];
}

// Test API key if found
if (!empty($OPENAI_API_KEY)) {
    $test_data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'user',
                'content' => 'Say "test"'
            ]
        ],
        'max_tokens' => 5
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
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    $debug_info['api_test'] = [
        'http_code' => $http_code,
        'curl_error' => $curl_error ?: 'none',
        'response_preview' => substr($response, 0, 200)
    ];
    
    if ($http_code === 200) {
        $response_data = json_decode($response, true);
        $debug_info['api_test']['success'] = true;
        $debug_info['api_test']['response'] = $response_data['choices'][0]['message']['content'] ?? 'No content';
    } else {
        $error_data = json_decode($response, true);
        $debug_info['api_test']['success'] = false;
        $debug_info['api_test']['error'] = $error_data['error'] ?? 'Unknown error';
    }
}

echo json_encode($debug_info, JSON_PRETTY_PRINT);

