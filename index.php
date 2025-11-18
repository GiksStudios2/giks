<?php
// Simple single-entry PHP web interface.
// Usage: place in your web root. Access pages via ?page=dashboard or ?page=settings
session_start();

define('DATA_DIR', __DIR__ . '/data');
define('SETTINGS_FILE', DATA_DIR . '/settings.json');

// Ensure data directory exists
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// Load settings (with safe defaults)
$defaultSettings = ['site_title' => 'My PHP Web Interface', 'admin_email' => 'admin@example.com'];
$settings = $defaultSettings;
if (file_exists(SETTINGS_FILE)) {
    $raw = file_get_contents(SETTINGS_FILE);
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $settings = array_merge($defaultSettings, $decoded);
    }
}

// Basic routing: ?page=dashboard (default) or ?page=settings
$page = isset($_GET['page']) ? basename($_GET['page']) : 'dashboard';
$allowed_pages = ['dashboard', 'settings'];

if (!in_array($page, $allowed_pages, true)) {
    http_response_code(404);
    echo "Page not found";
    exit;
}

// Provide $settings to pages
require __DIR__ . '/pages/header.php';
require __DIR__ . '/pages/' . $page . '.php';
require __DIR__ . '/pages/footer.php';
?>