<?php
// db_config.php

$host = 'localhost';
$db   = 'rpldb';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}


function log_system_action($pdo, $user_name, $action, $cible, $details) {
    try {
        $stmt = $pdo->prepare("INSERT INTO system_logs (utilisateur, action_type, cible, details, ip_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_name, 
            $action, 
            $cible, 
            $details, 
            $_SERVER['REMOTE_ADDR']
        ]);
    } catch (PDOException $e) {
        // Optionnel : logger l'erreur dans un fichier texte si l'insertion échoue
    }
}


?>

