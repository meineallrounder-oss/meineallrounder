<?php
/**
 * Test Script for OpenAI API Key
 * Use this to verify your API key is working correctly
 */

// Load configuration
$config = require __DIR__ . '/config.php';

echo "=== OpenAI API Key Test ===\n\n";

// Check config.php
echo "1. Checking config.php...\n";
if (isset($config['chatbot_settings']['openai_api_key']) && !empty($config['chatbot_settings']['openai_api_key'])) {
    $api_key = trim($config['chatbot_settings']['openai_api_key']);
    echo "   ✓ API Key found in config.php\n";
    echo "   ✓ Key length: " . strlen($api_key) . " characters\n";
    echo "   ✓ Key starts with: " . substr($api_key, 0, 7) . "...\n";
} else {
    echo "   ✗ API Key NOT found in config.php\n";
    $api_key = '';
}

// Check .env files
if (empty($api_key)) {
    echo "\n2. Checking .env files...\n";
    $env_paths = [
        __DIR__ . '/.env',
        __DIR__ . '/env',
        __DIR__ . '/../.env',
    ];
    
    foreach ($env_paths as $env_path) {
        if (file_exists($env_path)) {
            echo "   Found: $env_path\n";
            $env_file = file_get_contents($env_path);
            preg_match('/OPENAI_API_KEY\s*=\s*(.+)/', $env_file, $matches);
            if (!empty($matches[1])) {
                $api_key = trim($matches[1]);
                echo "   ✓ API Key found in $env_path\n";
                echo "   ✓ Key length: " . strlen($api_key) . " characters\n";
                break;
            }
        }
    }
}

// Test API key
if (!empty($api_key)) {
    echo "\n3. Testing API Key with OpenAI...\n";
    
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
        'Authorization: Bearer ' . $api_key
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    if ($curl_error) {
        echo "   ✗ Connection error: $curl_error\n";
    } elseif ($http_code === 200) {
        $response_data = json_decode($response, true);
        if (isset($response_data['choices'][0]['message']['content'])) {
            echo "   ✓ API Key is VALID!\n";
            echo "   ✓ Response: " . trim($response_data['choices'][0]['message']['content']) . "\n";
        } else {
            echo "   ✗ Invalid response format\n";
            echo "   Response: " . substr($response, 0, 200) . "\n";
        }
    } else {
        $error_data = json_decode($response, true);
        $error_message = $error_data['error']['message'] ?? 'Unknown error';
        $error_type = $error_data['error']['type'] ?? 'unknown';
        
        echo "   ✗ API Key test FAILED\n";
        echo "   HTTP Code: $http_code\n";
        echo "   Error Type: $error_type\n";
        echo "   Error Message: $error_message\n";
        
        if ($error_type === 'invalid_api_key') {
            echo "\n   ⚠ WARNING: The API key appears to be invalid!\n";
        } elseif ($error_type === 'insufficient_quota') {
            echo "\n   ⚠ WARNING: The API key has no quota remaining!\n";
        }
    }
} else {
    echo "\n3. Cannot test - No API key found!\n";
}

echo "\n=== Test Complete ===\n";

