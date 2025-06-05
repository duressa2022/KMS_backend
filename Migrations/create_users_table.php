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
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql);
    // Insert sample admin user
    $adminEmail = 'admin@ginjoguduru.gov';
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT); // Default password: admin123
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password, role) 
        VALUES (:email, :password, 'admin')
        ON DUPLICATE KEY UPDATE email = email;
    ");
    $stmt->execute(['email' => $adminEmail, 'password' => $adminPassword]);
    echo "✅ Users table created and admin user inserted.\n";
} catch (PDOException $e) {
    echo "❌ DB Error: " . $e->getMessage() . "\n";
}