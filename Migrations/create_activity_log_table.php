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
    CREATE TABLE IF NOT EXISTS activity_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        action_type ENUM('individual_added', 'house_added', 'id_card_issued', 'family_updated') NOT NULL,
        description VARCHAR(255) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql);
    echo "✅ Activity log table created successfully.\n";

    // Insert sample activity logs
    $stmt = $pdo->prepare("
        INSERT INTO activity_log (action_type, description, created_at) VALUES
        ('individual_added', 'Kebele Alemayehu was added to the system', NOW() - INTERVAL 10 MINUTE),
        ('house_added', 'House #6-245 was registered in Zone 3', NOW() - INTERVAL 35 MINUTE),
        ('id_card_issued', 'ID Card #66-22345 was issued to Fatima Mohammed', NOW() - INTERVAL 2 HOUR),
        ('family_updated', 'The Abebe family record was updated', NOW() - INTERVAL 4 HOUR)
    ");
    $stmt->execute();
    echo "✅ Sample activity logs inserted.\n";
} catch (PDOException $e) {
    echo "❌ DB Error: " . $e->getMessage() . "\n";
}