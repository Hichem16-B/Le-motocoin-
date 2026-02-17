<?php
$host = '127.0.0.1'; // Obligatoire sur macOS
$dbname = 'lemotocoin';
$username = 'root';
$password = 'root'; // Votre mot de passe MySQL
$port = '3306'; // Port par défaut
$socket = '/tmp/mysql.sock'; // Chemin du socket MySQL
$socket = '/Applications/MAMP/tmp/mysql/mysql.sock';
$pdo = new PDO("mysql:unix_socket=$socket;dbname=$dbname", 'root', 'root');
try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;unix_socket=$socket;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "Connexion réussie!"; // À supprimer en production
} catch (PDOException $e) {
    die("Erreur de connexion (Code ".$e->getCode()."): ".$e->getMessage());
}
?>