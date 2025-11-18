<?php
// lib/db.php
// SQLite initialization and helper functions.
// The DB file will be created automatically in data/app.sqlite

function db_init(): PDO {
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }

    $dbFile = $dataDir . '/app.sqlite';
    $dsn = 'sqlite:' . $dbFile;

    // Create PDO connection
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Use associative arrays by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Enable WAL for better concurrency
    $pdo->exec('PRAGMA journal_mode = WAL;');
    // Foreign keys on
    $pdo->exec('PRAGMA foreign_keys = ON;');

    // Create users table if not exists
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'user',
    created_at TEXT NOT NULL
);
SQL
    );

    // Optional settings table (simple key/value)
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
);
SQL
    );

    return $pdo;
}

function admin_exists(PDO $pdo): bool {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
    $stmt->execute(['admin']);
    return (int)$stmt->fetchColumn() > 0;
}

function create_user(PDO $pdo, string $username, string $email, string $password, string $role = 'user'): bool {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, ?)');
    return (bool)$stmt->execute([$username, $email, $hash, $role, date('c')]);
}

function get_user_by_username(PDO $pdo, string $username): ?array {
    $stmt = $pdo->prepare('SELECT id, username, email, password, role, created_at FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function get_all_users(PDO $pdo): array {
    $stmt = $pdo->query('SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

function verify_credentials(PDO $pdo, string $username, string $password): ?array {
    $user = get_user_by_username($pdo, $username);
    if (!$user) return null;
    if (password_verify($password, $user['password'])) {
        // Optional: rehash if needed
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([$newHash, $user['id']]);
        }
        return $user;
    }
    return null;
}
?>