<?php
// Entry point and router with auth & DB init
session_start();
// Secure session cookie settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

require_once __DIR__ . '/lib/db.php';
$pdo = db_init();

// Helper: current user
function current_user($pdo) {
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare('SELECT id, username, email, role FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    return null;
}

$user = current_user($pdo);
$admin_exists = admin_exists($pdo);

// Routing
$page = isset($_GET['page']) ? basename($_GET['page']) : '';
$public_pages = ['login', 'logout'];

// If no admin exists, force register page
if (!$admin_exists) {
    $page = 'register';
}

// If admin exists and not logged in, redirect to login for protected pages
$allowed_pages = ['dashboard', 'settings', 'login', 'logout', 'register'];
if ($page === '') {
    $page = $admin_exists ? 'login' : 'register';
}
if (!in_array($page, $allowed_pages, true)) {
    http_response_code(404);
    echo "Page not found";
    exit;
}

// If admin exists and page is register, block access
if ($admin_exists && $page === 'register') {
    header('Location: ?page=login');
    exit;
}

// Protect dashboard/settings: require login and admin role
if (in_array($page, ['dashboard', 'settings']) && (!$user || ($user['role'] !== 'admin'))) {
    header('Location: ?page=login');
    exit;
}

// CSRF token helper
if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(16));
}

require __DIR__ . '/pages/header.php';
require __DIR__ . '/pages/' . $page . '.php';
require __DIR__ . '/pages/footer.php';
?>