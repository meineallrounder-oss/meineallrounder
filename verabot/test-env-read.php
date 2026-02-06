<?php
/**
 * Test script to check if env file is being read correctly
 */

echo "=== Testing env file reading ===\n\n";

// Test 1: Check if files exist
echo "1. Checking file existence:\n";
$env_paths = [
    __DIR__ . '/.env',
    __DIR__ . '/env',
];

foreach ($env_paths as $path) {
    $exists = file_exists($path);
    echo "   " . basename($path) . ": " . ($exists ? "✓ EXISTS" : "✗ NOT FOUND") . "\n";
    if ($exists) {
        echo "      Full path: $path\n";
        echo "      Size: " . filesize($path) . " bytes\n";
        echo "      Readable: " . (is_readable($path) ? "YES" : "NO") . "\n";
    }
}

// Test 2: Try to read env file
echo "\n2. Reading env file:\n";
$env_file = __DIR__ . '/env';
if (file_exists($env_file)) {
    $content = file_get_contents($env_file);
    echo "   Content length: " . strlen($content) . " bytes\n";
    echo "   First 50 chars: " . substr($content, 0, 50) . "...\n";
    
    // Test regex
    preg_match('/OPENAI_API_KEY\s*=\s*(.+)/', $content, $matches);
    if (!empty($matches[1])) {
        $api_key = trim($matches[1]);
        echo "   ✓ API Key found!\n";
        echo "   Key length: " . strlen($api_key) . " characters\n";
        echo "   Key preview: " . substr($api_key, 0, 10) . "..." . substr($api_key, -10) . "\n";
    } else {
        echo "   ✗ API Key NOT found in env file!\n";
        echo "   Content: " . $content . "\n";
    }
} else {
    echo "   ✗ env file does not exist!\n";
}

// Test 3: Check config.php
echo "\n3. Checking config.php:\n";
$config = require __DIR__ . '/config.php';
if (isset($config['chatbot_settings']['openai_api_key']) && !empty($config['chatbot_settings']['openai_api_key'])) {
    $api_key = $config['chatbot_settings']['openai_api_key'];
    echo "   ✓ API Key found in config.php!\n";
    echo "   Key length: " . strlen($api_key) . " characters\n";
    echo "   Key preview: " . substr($api_key, 0, 10) . "..." . substr($api_key, -10) . "\n";
} else {
    echo "   ✗ API Key NOT found in config.php!\n";
}

// Test 4: Simulate API endpoint logic
echo "\n4. Simulating API endpoint logic:\n";
$OPENAI_API_KEY = '';

// Check config.php first
if (isset($config['chatbot_settings']['openai_api_key']) && !empty($config['chatbot_settings']['openai_api_key'])) {
    $OPENAI_API_KEY = trim($config['chatbot_settings']['openai_api_key']);
    echo "   ✓ Found in config.php (highest priority)\n";
}

// If not in config, try env files
if (empty($OPENAI_API_KEY)) {
    echo "   Checking env files...\n";
    foreach ($env_paths as $env_path) {
        if (file_exists($env_path)) {
            echo "   Checking: $env_path\n";
            $env_content = file_get_contents($env_path);
            preg_match('/OPENAI_API_KEY\s*=\s*(.+)/', $env_content, $matches);
            if (!empty($matches[1])) {
                $OPENAI_API_KEY = trim($matches[1]);
                echo "   ✓ Found in " . basename($env_path) . "\n";
                break;
            }
        }
    }
}

if (empty($OPENAI_API_KEY)) {
    echo "   ✗ No API key found anywhere!\n";
} else {
    echo "   ✓ Final API key: " . substr($OPENAI_API_KEY, 0, 10) . "..." . substr($OPENAI_API_KEY, -10) . "\n";
}

echo "\n=== Test Complete ===\n";



