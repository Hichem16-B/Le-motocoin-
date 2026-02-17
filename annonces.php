<?php
// Connexion PDO en une ligne avec gestion d'erreur
$dsn = "mysql:host=localhost;dbname=lemotocoin;charset=utf8";
$user = "root";
$pass = "root";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    // echo "Connexion réussie à la base de données gestionstock.";
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des annonces</title>
    <style>
        .annonce { border:1px solid #ddd; padding:15px; margin-bottom:20px; }
        .annonce img { max-width:200px; display:block; margin-top:10px; }
    </style>
</head>
<body>
    <h1>Liste des annonces</h1>
    
    <?php if (!empty($annonces)): ?>
        <?php foreach ($annonces as $annonce): ?>
            <div class="annonce">
                <h2>Modèle : <?= htmlspecialchars($annonce['modele']) ?></h2>
                <p><strong>Prix :</strong> <?= htmlspecialchars($annonce['prix']) ?> €</p>
                <p><strong>Année :</strong> <?= htmlspecialchars($annonce['annee']) ?></p>
                <p><strong>Couleur :</strong> <?= htmlspecialchars($annonce['couleur']) ?></p>
                <p><strong>Kilométrage :</strong> <?= htmlspecialchars($annonce['kilometrage']) ?> km</p>
                <p><strong>Description :</strong><br><?= nl2br(htmlspecialchars($annonce['description'])) ?></p>
                
                <?php if (!empty($annonce['photo'])): ?>
                    <img src="uploads/<?= htmlspecialchars($annonce['photo']) ?>" alt="Photo du véhicule">
                <?php endif; ?>
                
                <p><em>Vendu par : <?= htmlspecialchars($annonce['prenom'].' '.$annonce['nom']) ?></em></p>
                
                <?php if (isset($_SESSION['idvendeur']) && $_SESSION['idvendeur'] == $annonce['idvendeur']): ?>
                    <div style="margin-top:10px;">
                        <a href="modifier_annonce.php?id=<?= $annonce['idannonce'] ?>">Modifier</a> | 
                        <a href="supprimer_annonce.php?id=<?= $annonce['idannonce'] ?>" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce?')">
                           Supprimer
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune annonce disponible pour le moment.</p>
    <?php endif; ?>
    
    
    <?php if (isset($_SESSION['idvendeur'])): ?>
        <p><a href="creer_annonce.php">Créer une nouvelle annonce</a></p>
    <?php endif; ?>
</body>
</html>