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
    CREATE TABLE IF NOT EXISTS families (
        id INT AUTO_INCREMENT PRIMARY KEY,
        family_number VARCHAR(50) NOT NULL UNIQUE,
        house_number VARCHAR(50) NOT NULL,
        head_first_name VARCHAR(100) NOT NULL,
        head_last_name VARCHAR(100) NOT NULL,
        head_gender VARCHAR(20) NOT NULL,
        head_id VARCHAR(50),
        head_phone VARCHAR(20) NOT NULL,
        zone VARCHAR(100) NOT NULL,
        kebele VARCHAR(100) NOT NULL,
        city VARCHAR(100) NOT NULL,
        region VARCHAR(100) NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    );

    CREATE TABLE IF NOT EXISTS family_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        family_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        relationship VARCHAR(50) NOT NULL,
        id_number VARCHAR(50),
        FOREIGN KEY (family_id) REFERENCES families(id) ON DELETE CASCADE
    );
    ";

    $pdo->exec($sql);
    echo "✅ Families and family_members tables created successfully.\n";
} catch (PDOException $e) {
    echo "❌ DB Error: " . $e->getMessage() . "\n";
}