<?php
require_once 'db.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['idvendeur'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si l'ID de l'annonce est présent
if (!isset($_GET['id'])) {
    header('Location: annonces.php');
    exit();
}

$idannonce = $_GET['id'];

// Vérifier que l'annonce appartient bien à l'utilisateur connecté
try {
    $stmt = $pdo->prepare("SELECT photo FROM Annonces WHERE idannonce = ? AND idvendeur = ?");
    $stmt->execute([$idannonce, $_SESSION['idvendeur']]);
    $annonce = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$annonce) {
        header('Location: annonces.php');
        exit();
    }
} catch (PDOException $e) {
    die("Erreur lors de la vérification de l'annonce: " . $e->getMessage());
}

// Suppression de l'annonce
try {
    // Supprimer la photo associée si elle existe
    if (!empty($annonce['photo']) && file_exists('uploads/' . $annonce['photo'])) {
        unlink('uploads/' . $annonce['photo']);
    }
    
    // Supprimer l'annonce de la base de données
    $stmt = $pdo->prepare("DELETE FROM Annonces WHERE idannonce = ?");
    $stmt->execute([$idannonce]);
    
    header('Location: annonces.php?delete_success=1');
    exit();
} catch (PDOException $e) {
    die("Erreur lors de la suppression de l'annonce: " . $e->getMessage());
}
?>