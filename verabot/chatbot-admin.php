<?php
/**
 * Chatbot Admin Panel - Settings & Configuration
 * Vera IT - Customize Chatbot Appearance & Settings
 * 
 * SECURITY: Uses same password as chat-admin.php
 */

session_start();

// Same password as chat-admin.php
$admin_password = 'vera75433'; // ⚠️ CHANGE THIS PASSWORD!

// Handle login
if (isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['chat_admin_logged_in'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['login_ip'] = $_SERVER['REMOTE_ADDR'];
    } else {
        $error = 'Falsches Passwort!';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: chatbot-admin.php');
    exit;
}

// Check if logged in
if (!isset($_SESSION['chat_admin_logged_in']) || !$_SESSION['chat_admin_logged_in']) {
    ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chatbot Admin Login - Vera IT</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .login-box {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 215, 0, 0.2);
                border-radius: 16px;
                padding: 40px;
                max-width: 400px;
                width: 100%;
                backdrop-filter: blur(10px);
            }
            h1 {
                color: #FFD700;
                margin-bottom: 10px;
                font-size: 24px;
            }
            p {
                color: rgba(255, 255, 255, 0.7);
                margin-bottom: 30px;
                font-size: 14px;
            }
            input[type="password"] {
                width: 100%;
                padding: 12px 16px;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 8px;
                color: #fff;
                font-size: 16px;
                margin-bottom: 20px;
            }
            input[type="password"]:focus {
                outline: none;
                border-color: #FFD700;
            }
            button {
                width: 100%;
                padding: 12px;
                background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
                border: none;
                border-radius: 8px;
                color: #0a0e27;
                font-weight: 600;
                font-size: 16px;
                cursor: pointer;
                transition: transform 0.2s;
            }
            button:hover {
                transform: translateY(-2px);
            }
            .error {
                color: #ff6b6b;
                margin-bottom: 20px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h1>🔒 Chatbot Admin</h1>
            <p>Bitte geben Sie das Passwort ein</p>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Passwort" required autofocus>
                <button type="submit">Anmelden</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Load current config
$config_file = __DIR__ . '/config.php';
$config = require $config_file;

// Get current settings
$settings = isset($config['chatbot_settings']) ? $config['chatbot_settings'] : [
    'header_color' => '#ea580c',
    'header_color_secondary' => '#fb923c',
    'user_message_color' => '#ea580c',
    'user_message_color_secondary' => '#fb923c',
    'toggle_button_color' => '#ea580c',
    'background_color' => '#ffffff',
    'universal_color' => '#ea580c',
    'icon_emoji' => '💬',
    'logo_url' => '',
    'openai_api_key' => ''
];

// If API key is not in settings, try to read from .env or env file for display
if (empty($settings['openai_api_key'])) {
    $env_paths = [
        __DIR__ . '/.env',
        __DIR__ . '/env'  // Also check 'env' without dot
    ];
    
    foreach ($env_paths as $env_file) {
        if (file_exists($env_file)) {
            $env_content = file_get_contents($env_file);
            if (preg_match('/OPENAI_API_KEY\s*=\s*(.+)/', $env_content, $matches)) {
                $settings['openai_api_key'] = trim($matches[1]);
                break;
            }
        }
    }
}

// Handle save
$save_success = false;
$save_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    // Update settings
    $settings = [
        'header_color' => sanitize($_POST['header_color'] ?? $settings['header_color']),
        'header_color_secondary' => sanitize($_POST['header_color_secondary'] ?? $settings['header_color_secondary']),
        'user_message_color' => sanitize($_POST['user_message_color'] ?? $settings['user_message_color']),
        'user_message_color_secondary' => sanitize($_POST['user_message_color_secondary'] ?? $settings['user_message_color_secondary']),
        'toggle_button_color' => sanitize($_POST['toggle_button_color'] ?? $settings['toggle_button_color']),
        'background_color' => sanitize($_POST['background_color'] ?? $settings['background_color']),
        'universal_color' => sanitize($_POST['universal_color'] ?? $settings['universal_color']),
        'icon_emoji' => sanitize($_POST['icon_emoji'] ?? $settings['icon_emoji']),
        'logo_url' => sanitize($_POST['logo_url'] ?? $settings['logo_url']),
        'openai_api_key' => sanitize($_POST['openai_api_key'] ?? $settings['openai_api_key'])
    ];
    
    // Save to config.php
    $config_content = file_get_contents($config_file);
    
    // Check if chatbot_settings exists, if not add it
    if (strpos($config_content, "'chatbot_settings'") === false) {
        // Add before closing array
        $config_content = preg_replace('/\];\s*$/', "    'chatbot_settings' => " . var_export($settings, true) . ",\n];", $config_content);
    } else {
        // Update existing settings
        $config_content = preg_replace(
            "/'chatbot_settings'\s*=>\s*\[.*?\],/s",
            "'chatbot_settings' => " . var_export($settings, true) . ",",
            $config_content
        );
    }
    
    // Save OpenAI API key to .env or env file
    if (!empty($settings['openai_api_key'])) {
        // Try .env first, then env (without dot)
        $env_paths = [
            __DIR__ . '/.env',
            __DIR__ . '/env'
        ];
        
        $env_file = null;
        foreach ($env_paths as $env_path) {
            if (file_exists($env_path)) {
                $env_file = $env_path;
                break;
            }
        }
        
        // If no env file exists, create .env
        if (!$env_file) {
            $env_file = __DIR__ . '/.env';
        }
        
        $env_content = '';
        if (file_exists($env_file)) {
            $env_content = file_get_contents($env_file);
            if (preg_match('/OPENAI_API_KEY\s*=/', $env_content)) {
                $env_content = preg_replace('/OPENAI_API_KEY\s*=.*/m', 'OPENAI_API_KEY=' . $settings['openai_api_key'], $env_content);
            } else {
                $env_content .= "\nOPENAI_API_KEY=" . $settings['openai_api_key'];
            }
        } else {
            $env_content = 'OPENAI_API_KEY=' . $settings['openai_api_key'];
        }
        
        @file_put_contents($env_file, $env_content); // @ to suppress errors if no write permissions
    }
    
    // Write updated config
    if (file_put_contents($config_file, $config_content)) {
        $save_success = true;
        // Reload config
        $config = require $config_file;
        $settings = $config['chatbot_settings'] ?? $settings;
    } else {
        $save_error = 'Fehler beim Speichern der Einstellungen. Bitte überprüfen Sie die Dateiberechtigungen.';
    }
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Get available icon options (💬 is default, so it's first)
$icon_options = ['💬', '🤖', '🦾', '✨', '⚡', '🎯', '🚀', '💡', '🌟', '🎨', '🔮', '⚙️'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Settings - Vera IT Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #0a0e27;
            color: #fff;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        h1 {
            color: #FFD700;
            font-size: 28px;
        }
        .nav-links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .nav-links a {
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        .nav-links a:hover {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #0a0e27;
            border-color: #FFD700;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .setting-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px;
            padding: 20px;
        }
        .setting-card h3 {
            color: #FFD700;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }
        .form-group input[type="text"],
        .form-group input[type="color"],
        .form-group input[type="url"],
        .form-group input[type="password"],
        .form-group select {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
        }
        .form-group input[type="color"] {
            height: 50px;
            cursor: pointer;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #FFD700;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
        }
        .icon-preview {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .icon-option {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
            border-radius: 12px;
            cursor: pointer;
            font-size: 24px;
            transition: all 0.3s;
        }
        .icon-option:hover {
            border-color: #FFD700;
            background: rgba(255, 215, 0, 0.2);
            transform: scale(1.1);
        }
        .icon-option.selected {
            border-color: #FFD700;
            background: rgba(255, 215, 0, 0.3);
        }
        .save-button {
            padding: 15px 40px;
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #0a0e27;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            margin-top: 20px;
        }
        .save-button:hover {
            transform: translateY(-2px);
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.5);
            color: #86efac;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #fca5a5;
        }
        .preview-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
        }
        .preview-section h3 {
            color: #FFD700;
            margin-bottom: 15px;
        }
        .color-preview {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .color-box {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ Chatbot Settings</h1>
            <div class="nav-links">
                <a href="chatbot-admin.php">⚙️ Settings</a>
                <a href="chat-admin.php">💬 Logs</a>
                <a href="?logout=1">🚪 Abmelden</a>
            </div>
        </div>

        <?php if ($save_success): ?>
            <div class="alert alert-success">
                ✅ Einstellungen erfolgreich gespeichert!
            </div>
        <?php endif; ?>

        <?php if ($save_error): ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($save_error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="settings-grid">
                <!-- Colors Section -->
                <div class="setting-card">
                    <h3>🎨 Farben</h3>
                    
                    <div class="form-group">
                        <label>Universal Farbe (Primär)</label>
                        <input type="color" name="universal_color" value="<?php echo htmlspecialchars($settings['universal_color']); ?>" id="universal_color">
                        <input type="text" value="<?php echo htmlspecialchars($settings['universal_color']); ?>" onchange="document.getElementById('universal_color').value = this.value; updatePreview();" style="margin-top: 8px;">
                    </div>

                    <div class="form-group">
                        <label>Header Farbe (Primär)</label>
                        <input type="color" name="header_color" value="<?php echo htmlspecialchars($settings['header_color']); ?>" id="header_color" onchange="updatePreview();">
                        <input type="text" value="<?php echo htmlspecialchars($settings['header_color']); ?>" onchange="document.getElementById('header_color').value = this.value; updatePreview();" style="margin-top: 8px;">
                    </div>

                    <div class="form-group">
                        <label>Header Farbe (Sekundär)</label>
                        <input type="color" name="header_color_secondary" value="<?php echo htmlspecialchars($settings['header_color_secondary']); ?>" id="header_color_secondary" onchange="updatePreview();">
                        <input type="text" value="<?php echo htmlspecialchars($settings['header_color_secondary']); ?>" onchange="document.getElementById('header_color_secondary').value = this.value; updatePreview();" style="margin-top: 8px;">
                    </div>

                    <div class="form-group">
                        <label>User Nachricht Farbe (Primär)</label>
                        <input type="color" name="user_message_color" value="<?php echo htmlspecialchars($settings['user_message_color']); ?>" id="user_message_color" onchange="updatePreview();">
                        <input type="text" value="<?php echo htmlspecialchars($settings['user_message_color']); ?>" onchange="document.getElementById('user_message_color').value = this.value; updatePreview();" style="margin-top: 8px;">
                    </div>

                    <div class="form-group">
                        <label>User Nachricht Farbe (Sekundär)</label>
                        <input type="color" name="user_message_color_secondary" value="<?php echo htmlspecialchars($settings['user_message_color_secondary']); ?>" id="user_message_color_secondary" onchange="updatePreview();">
                        <input type="text" value="<?php echo htmlspecialchars($settings['user_message_color_secondary']); ?>" onchange="document.getElementById('user_message_color_secondary').value = this.value; updatePreview();" style="margin-top: 8px;">
                    </div>

                    <div class="form-group">
                        <label>Toggle Button Farbe</label>
                        <input type="color" name="toggle_button_color" value="<?php echo htmlspecialchars($settings['toggle_button_color']); ?>" id="toggle_button_color" onchange="updatePreview();">
                        <input type="text" value="<?php echo htmlspecialchars($settings['toggle_button_color']); ?>" onchange="document.getElementById('toggle_button_color').value = this.value; updatePreview();" style="margin-top: 8px;">
                    </div>

                    <div class="form-group">
                        <label>Hintergrund Farbe</label>
                        <input type="color" name="background_color" value="<?php echo htmlspecialchars($settings['background_color']); ?>" id="background_color" onchange="updatePreview();">
                        <input type="text" value="<?php echo htmlspecialchars($settings['background_color']); ?>" onchange="document.getElementById('background_color').value = this.value; updatePreview();" style="margin-top: 8px;">
                    </div>
                </div>

                <!-- Appearance Section -->
                <div class="setting-card">
                    <h3>✨ Aussehen</h3>
                    
                    <div class="form-group">
                        <label>Icon / Emoji</label>
                        <div class="icon-preview">
                            <?php foreach ($icon_options as $icon): ?>
                                <div class="icon-option <?php echo $settings['icon_emoji'] === $icon ? 'selected' : ''; ?>" 
                                     onclick="selectIcon('<?php echo $icon; ?>')">
                                    <?php echo $icon; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="icon_emoji" id="icon_emoji" value="<?php echo htmlspecialchars($settings['icon_emoji']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Logo URL (Optional)</label>
                        <input type="url" name="logo_url" value="<?php echo htmlspecialchars($settings['logo_url']); ?>" placeholder="https://example.com/logo.png">
                        <small style="color: rgba(255,255,255,0.5); display: block; margin-top: 5px;">
                            Falls leer, wird das Icon/Emoji verwendet
                        </small>
                    </div>
                </div>

                <!-- API Settings -->
                <div class="setting-card">
                    <h3>🔑 API Einstellungen</h3>
                    
                    <div class="form-group">
                        <label>OpenAI API Key</label>
                        <div style="position: relative;">
                            <input type="password" name="openai_api_key" id="openai_key" value="<?php echo htmlspecialchars($settings['openai_api_key']); ?>" placeholder="sk-..." style="padding-right: 100px;">
                            <button type="button" onclick="toggleApiKeyVisibility()" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; padding: 6px 12px; color: #fff; cursor: pointer; font-size: 12px;">
                                <span id="toggle-btn-text">👁️ Anzeigen</span>
                            </button>
                        </div>
                        <small style="color: rgba(255,255,255,0.5); display: block; margin-top: 5px;">
                            Wird in .env Datei und config.php gespeichert
                        </small>
                        <?php if (!empty($settings['openai_api_key'])): ?>
                            <div style="margin-top: 8px; padding: 8px; background: rgba(34, 197, 94, 0.2); border: 1px solid rgba(34, 197, 94, 0.5); border-radius: 6px; font-size: 12px; color: #86efac;">
                                ✅ API Key ist gespeichert (<?php echo substr($settings['openai_api_key'], 0, 7) . '...' . substr($settings['openai_api_key'], -4); ?>)
                            </div>
                        <?php else: ?>
                            <div style="margin-top: 8px; padding: 8px; background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.5); border-radius: 6px; font-size: 12px; color: #fca5a5;">
                                ⚠️ Kein API Key gesetzt - Chatbot wird nicht funktionieren
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <button type="submit" name="save_settings" class="save-button">💾 Einstellungen Speichern</button>
        </form>

        <div class="preview-section">
            <h3>👁️ Farbvorschau</h3>
            <div class="color-preview">
                <div>
                    <div class="color-box" id="preview-universal" style="background: <?php echo htmlspecialchars($settings['universal_color']); ?>;"></div>
                    <div style="text-align: center; margin-top: 5px; font-size: 12px; color: rgba(255,255,255,0.6);">Universal</div>
                </div>
                <div>
                    <div class="color-box" id="preview-header" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($settings['header_color']); ?> 0%, <?php echo htmlspecialchars($settings['header_color_secondary']); ?> 100%);"></div>
                    <div style="text-align: center; margin-top: 5px; font-size: 12px; color: rgba(255,255,255,0.6);">Header</div>
                </div>
                <div>
                    <div class="color-box" id="preview-user" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($settings['user_message_color']); ?> 0%, <?php echo htmlspecialchars($settings['user_message_color_secondary']); ?> 100%);"></div>
                    <div style="text-align: center; margin-top: 5px; font-size: 12px; color: rgba(255,255,255,0.6);">User Nachricht</div>
                </div>
                <div>
                    <div class="color-box" id="preview-toggle" style="background: <?php echo htmlspecialchars($settings['toggle_button_color']); ?>;"></div>
                    <div style="text-align: center; margin-top: 5px; font-size: 12px; color: rgba(255,255,255,0.6);">Toggle Button</div>
                </div>
                <div>
                    <div class="color-box" id="preview-bg" style="background: <?php echo htmlspecialchars($settings['background_color']); ?>;"></div>
                    <div style="text-align: center; margin-top: 5px; font-size: 12px; color: rgba(255,255,255,0.6);">Hintergrund</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectIcon(icon) {
            document.getElementById('icon_emoji').value = icon;
            document.querySelectorAll('.icon-option').forEach(el => el.classList.remove('selected'));
            event.target.closest('.icon-option').classList.add('selected');
        }

        function updatePreview() {
            const universal = document.getElementById('universal_color').value;
            const header1 = document.getElementById('header_color').value;
            const header2 = document.getElementById('header_color_secondary').value;
            const user1 = document.getElementById('user_message_color').value;
            const user2 = document.getElementById('user_message_color_secondary').value;
            const toggle = document.getElementById('toggle_button_color').value;
            const bg = document.getElementById('background_color').value;

            document.getElementById('preview-universal').style.background = universal;
            document.getElementById('preview-header').style.background = `linear-gradient(135deg, ${header1} 0%, ${header2} 100%)`;
            document.getElementById('preview-user').style.background = `linear-gradient(135deg, ${user1} 0%, ${user2} 100%)`;
            document.getElementById('preview-toggle').style.background = toggle;
            document.getElementById('preview-bg').style.background = bg;
        }

        // Sync color inputs
        document.querySelectorAll('input[type="color"]').forEach(colorInput => {
            colorInput.addEventListener('input', function() {
                const textInput = this.parentElement.querySelector('input[type="text"]');
                if (textInput) {
                    textInput.value = this.value;
                }
                updatePreview();
            });
        });

        document.querySelectorAll('input[type="text"]').forEach(textInput => {
            if (textInput.previousElementSibling && textInput.previousElementSibling.type === 'color') {
                textInput.addEventListener('input', function() {
                    const colorInput = this.previousElementSibling;
                    if (colorInput && /^#[0-9A-F]{6}$/i.test(this.value)) {
                        colorInput.value = this.value;
                        updatePreview();
                    }
                });
            }
        });

        // Toggle API Key visibility
        function toggleApiKeyVisibility() {
            const keyInput = document.getElementById('openai_key');
            const toggleBtn = document.getElementById('toggle-btn-text');
            
            if (keyInput.type === 'password') {
                keyInput.type = 'text';
                toggleBtn.textContent = '🙈 Verbergen';
            } else {
                keyInput.type = 'password';
                toggleBtn.textContent = '👁️ Anzeigen';
            }
        }
    </script>
</body>
</html>

