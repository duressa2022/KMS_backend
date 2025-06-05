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
    CREATE TABLE IF NOT EXISTS individuals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        date_of_birth DATE NOT NULL,
        age INT NOT NULL,
        gender VARCHAR(20) NOT NULL,
        religion VARCHAR(50) NOT NULL,
        nationality VARCHAR(100) NOT NULL,
        occupation VARCHAR(100) NOT NULL,
        education_level VARCHAR(50) NOT NULL,
        family_number VARCHAR(50) NOT NULL,
        house_number VARCHAR(50) NOT NULL,
        relationship_to_family_head VARCHAR(50) NOT NULL,
        phone_number VARCHAR(20) NOT NULL UNIQUE,
        email VARCHAR(255),
        photo_url TEXT,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    );
    ";

    $pdo->exec($sql);
    echo "✅ Individuals table created successfully.\n";
} catch (PDOException $e) {
    echo "❌ DB Error: " . $e->getMessage() . "\n";
}