<?php
/**
 * Chatbot Configuration
 * Customize this file with your company information
 */

return [
    'company_name' => 'Meine Allrounder',
    'website' => 'meineallrounder.de',
    'location' => 'Moers, Deutschland',
    'address' => 'Franzstr. 20, 47441 Moers, Deutschland',
    'founded' => '2024',
    'experience' => 'Professionelle Renovierungsarbeiten mit Qualität, Sauberkeit und Zuverlässigkeit',
    'owner' => [
        'name' => 'Rajko Durdevic',
        'title' => 'CEO',
        'role' => 'Geschäftsführer'
    ],
    'services' => [
        'Badsanierung',
        'Fliesenarbeiten',
        'Gipsarbeiten',
        'Renovierungsarbeiten',
        'Montagearbeiten',
        'Komplette Hausrenovierung'
    ],
    'contact' => [
        'email' => 'info@meineallrounder.de',
        'phone' => '+49 15211501980'
    ],
    'work_method' => [
        'Qualität' => 'Größter Wert auf Qualität und Präzision',
        'Sauberkeit' => 'Saubere Arbeitsweise und ordentliche Durchführung',
        'Termingerecht' => 'Zuverlässige Einhaltung von Terminen',
        'Nachhaltigkeit' => 'Nachhaltige Materialien und Arbeitsweisen'
    ],
    'ki_builder' => [
        'name' => 'Meine Allrounder KI Asistent',
        'description' => 'KI-gestützter Assistent für Bau- und Renovierungsanfragen',
        'purpose' => 'Unterstützung bei Fragen zu unseren Dienstleistungen',
        'features' => [
            '24/7 Verfügbarkeit',
            'Schnelle Antworten',
            'Professionelle Beratung',
            'Mehrsprachige Unterstützung'
        ],
        'url' => 'https://meineallrounder.de'
    ],
    'about' => 'Meine Allrounder ist ein professionelles Renovierungsunternehmen in Moers, Deutschland. Wir bieten hochwertige Renovierungsarbeiten für Haus und Wohnung mit Fokus auf Qualität, Sauberkeit und Kundenzufriedenheit.',
    'team' => 'Unser Team besteht aus erfahrenen Fachkräften unter der Leitung von Rajko Durdevic (CEO) und Jovica Mihajlovic (Marketing).',
    'specialization' => 'Wir spezialisieren uns auf Badsanierungen, Fliesenarbeiten, Gipsarbeiten und komplette Hausrenovierungen in Moers und Umgebung.',
    
    // Chatbot Appearance Settings (managed via chatbot-admin.php)
    'chatbot_settings' => [
        'header_color' => '#ea580c',
        'header_color_secondary' => '#fb923c',
        'user_message_color' => '#ea580c',
        'user_message_color_secondary' => '#fb923c',
        'toggle_button_color' => '#ea580c',
        'background_color' => '#ffffff',
        'universal_color' => '#ea580c',
        'icon_emoji' => '💬',
        'logo_url' => '',
        'openai_api_key' => 'getenv("OPENAI_API_KEY") ?: ""'
    ]
];

