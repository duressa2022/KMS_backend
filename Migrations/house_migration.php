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
    CREATE TABLE IF NOT EXISTS houses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        house_number VARCHAR(50) NOT NULL UNIQUE,
        area FLOAT NOT NULL,
        door_count INT NOT NULL,
        construction_year INT NOT NULL,
        house_type VARCHAR(50) NOT NULL,
        house_status VARCHAR(50) NOT NULL,
        owner_name VARCHAR(255) NOT NULL,
        owner_id VARCHAR(50),
        owner_phone VARCHAR(20) NOT NULL,
        zone VARCHAR(100) NOT NULL,
        kebele VARCHAR(100) NOT NULL,
        city VARCHAR(100) NOT NULL,
        region VARCHAR(100) NOT NULL,
        remarks TEXT,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    );
    ";

    $pdo->exec($sql);
    echo "✅ Houses table created successfully.\n";
} catch (PDOException $e) {
    echo "❌ DB Error: " . $e->getMessage() . "\n";
}