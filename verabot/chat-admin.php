<?php
/**
 * Chat Logs Admin Panel
 * Vera IT - View Chatbot Conversations
 * 
 * SECURITY: Change the password below!
 */

// Simple password protection
session_start();

// ⚠️ SECURITY: Change this password to something strong!
// You can use: password_hash() for better security (see below)
$admin_password = 'vera75433'; // ⚠️ CHANGE THIS PASSWORD!

// Optional: IP Whitelist (uncomment to restrict access to specific IPs)
// $allowed_ips = ['YOUR_IP_ADDRESS_HERE']; // Add your IP address
// if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
//     die('Access denied. Your IP: ' . $_SERVER['REMOTE_ADDR']);
// }

// Optional: Use password hash for better security (recommended)
// To use this, first generate a hash:
// Run: php -r "echo password_hash('your_strong_password', PASSWORD_DEFAULT);"
// Then replace $admin_password with the hash and uncomment the password_verify check below
// $admin_password_hash = '$2y$10$...'; // Generated hash from above command

// Handle login
if (isset($_POST['password'])) {
    // Simple password check (current method)
    if ($_POST['password'] === $admin_password) {
        $_SESSION['chat_admin_logged_in'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['login_ip'] = $_SERVER['REMOTE_ADDR'];
    } else {
        $error = 'Falsches Passwort!';
    }
    
    // Optional: Use password hash verification (more secure)
    // Uncomment this and comment out the simple check above if using password_hash
    // if (isset($admin_password_hash) && password_verify($_POST['password'], $admin_password_hash)) {
    //     $_SESSION['chat_admin_logged_in'] = true;
    //     $_SESSION['login_time'] = time();
    //     $_SESSION['login_ip'] = $_SERVER['REMOTE_ADDR'];
    // } else {
    //     $error = 'Falsches Passwort!';
    // }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: chat-admin.php');
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
        <title>Chat Admin Login - Vera IT</title>
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
            <h1>🔒 Chat Admin</h1>
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

// Load logs
$log_dir = __DIR__ . '/chat-logs';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$log_file = $log_dir . '/chat-' . $date . '.json';
$logs = [];
if (file_exists($log_file)) {
    $logs = json_decode(file_get_contents($log_file), true) ?: [];
}
$logs = array_reverse($logs); // Newest first

// Get available dates
$available_dates = [];
if (is_dir($log_dir)) {
    $files = glob($log_dir . '/chat-*.json');
    foreach ($files as $file) {
        if (preg_match('/chat-(\d{4}-\d{2}-\d{2})\.json$/', $file, $matches)) {
            $available_dates[] = $matches[1];
        }
    }
    rsort($available_dates);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Logs - Vera IT Admin</title>
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
        .controls {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        select, button {
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
        }
        select:focus, button:focus {
            outline: none;
            border-color: #FFD700;
        }
        button {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #0a0e27;
            font-weight: 600;
            border: none;
        }
        button:hover {
            opacity: 0.9;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px;
            padding: 20px;
        }
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #FFD700;
            margin-bottom: 5px;
        }
        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }
        .conversation {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-wrap: wrap;
            gap: 10px;
        }
        .conversation-time {
            color: #FFD700;
            font-weight: 600;
        }
        .conversation-meta {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
        }
        .message {
            margin-bottom: 15px;
        }
        .message-label {
            color: #FFD700;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .message-content {
            background: rgba(255, 255, 255, 0.05);
            border-left: 3px solid #FFD700;
            padding: 12px 16px;
            border-radius: 8px;
            line-height: 1.6;
        }
        .empty {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>💬 Chat Logs - <?php echo date('d.m.Y', strtotime($date)); ?></h1>
        <div class="controls">
            <select onchange="window.location.href='?date=' + this.value">
                <?php foreach ($available_dates as $d): ?>
                    <option value="<?php echo $d; ?>" <?php echo $d === $date ? 'selected' : ''; ?>>
                        <?php echo date('d.m.Y', strtotime($d)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button onclick="window.location.href='chatbot-admin.php'">⚙️ Settings</button>
            <button onclick="window.location.reload()">🔄 Aktualisieren</button>
            <button onclick="window.location.href='?logout=1'">🚪 Abmelden</button>
        </div>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-value"><?php echo count($logs); ?></div>
            <div class="stat-label">Konversationen heute</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo count($available_dates); ?></div>
            <div class="stat-label">Tage mit Logs</div>
        </div>
    </div>

    <?php if (empty($logs)): ?>
        <div class="empty">
            <p>Keine Konversationen für diesen Tag gefunden.</p>
        </div>
    <?php else: ?>
        <?php foreach ($logs as $log): ?>
            <div class="conversation">
                <div class="conversation-header">
                    <div>
                        <div class="conversation-time"><?php echo htmlspecialchars($log['time']); ?></div>
                        <div class="conversation-meta">
                            <?php echo htmlspecialchars($log['language']); ?> • 
                            IP: <?php echo htmlspecialchars($log['ip']); ?>
                        </div>
                    </div>
                </div>
                <div class="message">
                    <div class="message-label">👤 Benutzer:</div>
                    <div class="message-content"><?php echo nl2br(htmlspecialchars($log['user_message'])); ?></div>
                </div>
                <div class="message">
                    <div class="message-label">🤖 KI Antwort:</div>
                    <div class="message-content"><?php echo nl2br(htmlspecialchars($log['ai_response'])); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

