<?php

$host = $_ENV['DB_HOST'] ?? 'localhost';
$port = $_ENV['DB_PORT'] ?? '3306';
$db   = $_ENV['DB_NAME'] ?? 'kebele_admin';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $sql = "
    CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY DEFAULT 1,
        kebele_name VARCHAR(255) NOT NULL,
        admin_email VARCHAR(255) NOT NULL,
        timezone VARCHAR(100) NOT NULL,
        date_format VARCHAR(20) NOT NULL,
        email_notifications TINYINT(1) NOT NULL DEFAULT 1,
        audit_logging TINYINT(1) NOT NULL DEFAULT 0,
        show_recent_activity TINYINT(1) NOT NULL DEFAULT 1,
        require_strong_passwords TINYINT(1) NOT NULL DEFAULT 1,
        password_expiration TINYINT(1) NOT NULL DEFAULT 0,
        require_2fa TINYINT(1) NOT NULL DEFAULT 0,
        session_timeout INT NOT NULL DEFAULT 60,
        max_login_attempts INT NOT NULL DEFAULT 5,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        CHECK (id = 1)
    );
    ";

    $pdo->exec($sql);
    echo "✅ Settings table created successfully.\n";

    // Insert default settings if not exists
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO settings (
            id, kebele_name, admin_email, timezone, date_format, email_notifications,
            audit_logging, show_recent_activity, require_strong_passwords, password_expiration,
            require_2fa, session_timeout, max_login_attempts, created_at, updated_at
        ) VALUES (
            1, 'Ginjo Guduru Kebele Administration', 'admin@ginjoguduru.gov.et', 'Africa/Addis_Ababa',
            'mm/dd/yyyy', 1, 0, 1, 1, 0, 0, 60, 5, NOW(), NOW()
        )
    ");
    $stmt->execute();
    echo "✅ Default settings inserted.\n";
} catch (PDOException $e) {
    echo "❌ DB Error: " . $e->getMessage() . "\n";
}