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
    CREATE TABLE IF NOT EXISTS id_cards (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_number VARCHAR(50) NOT NULL UNIQUE,
        issue_date_idcard DATE NOT NULL,
        expiry_date_idcard DATE NOT NULL,
        idcard_type VARCHAR(50) NOT NULL,
        owner_id INT NOT NULL,
        photo_url TEXT,
        signature_url TEXT,
        blood_type VARCHAR(10),
        emergency_contact VARCHAR(255),
        remarks TEXT,
        created_at DATE NOT NULL,
        updated_at DATE NOT NULL,
        FOREIGN KEY (owner_id) REFERENCES individuals(id) ON DELETE CASCADE
    );
    ";

    $pdo->exec($sql);
    echo "✅ Id_cards table created successfully.\n";
} catch (PDOException $e) {
    echo "❌ DB Error: " . $e->getMessage() . "\n";
}