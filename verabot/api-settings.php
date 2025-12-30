<?php
/**
 * Chatbot Settings API
 * Returns chatbot appearance settings as JSON
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Load configuration
$config = require __DIR__ . '/config.php';

// Get chatbot settings
$settings = $config['chatbot_settings'] ?? [
    'header_color' => '#ea580c',
    'header_color_secondary' => '#fb923c',
    'user_message_color' => '#ea580c',
    'user_message_color_secondary' => '#fb923c',
    'toggle_button_color' => '#ea580c',
    'background_color' => '#ffffff',
    'universal_color' => '#ea580c',
    'icon_emoji' => '💬',
    'logo_url' => ''
];

echo json_encode($settings);

