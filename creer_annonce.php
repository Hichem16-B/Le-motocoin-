<?php
session_start();

// Connexion avec gestion d'erreurs
$dsn = "mysql:host=localhost;dbname=lemotocoin;charset=utf8";
$user = "root";
$pass = "root";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    // echo "Connexion réussie à la base de données .";
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}

// Traitement du formulaire
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage des données
    $donnees = [
        'modele' => htmlspecialchars(trim($_POST['modele'])),
        'prix' => floatval($_POST['prix']),
        'annee' => intval($_POST['annee']),
        'couleur' => htmlspecialchars(trim($_POST['couleur'])),
        'kilometrage' => intval($_POST['kilometrage']),
        'description' => htmlspecialchars(trim($_POST['description'])),
        'idvendeur' => 1 // À remplacer par $_SESSION['id'] plus tard
    ];

    // Gestion de la photo (version simplifiée mais sécurisée)
    if (!empty($_FILES['photo']['name'])) {
        $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        
        if (in_array($extension, $extensionsAutorisees)) {
            $dossierUpload = 'uploads/';
            if (!is_dir($dossierUpload)) mkdir($dossierUpload, 0755, true);
            
            $nomFichier = uniqid().'.'.$extension;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dossierUpload.$nomFichier)) {
                $donnees['photo'] = $dossierUpload.$nomFichier;
            } else {
                $error = "Erreur lors de l'upload de la photo";
            }
        } else {
            $error = "Format de photo non autorisé (seuls JPG, PNG, GIF)";
        }
    }

    if (empty($error)) {
        try {
            $sql = "INSERT INTO Annonces 
                    (modele, prix, annee, couleur, kilometrage, description, photo, idvendeur) 
                    VALUES (:modele, :prix, :annee, :couleur, :kilometrage, :description, :photo, :idvendeur)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($donnees);
            
            header('Location: annonces.php?success=1');
            exit();
        } catch (PDOException $e) {
            $error = "Erreur technique : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déposer une annonce</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: #d32f2f; background-color: #fde0e0; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Déposer une nouvelle annonce</h1>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="modele">Modèle* :</label>
            <input type="text" id="modele" name="modele" required>
        </div>
        
        <div class="form-group">
            <label for="prix">Prix (€)* :</label>
            <input type="number" id="prix" name="prix" step="0.01" min="0" required>
        </div>
        
        <div class="form-group">
            <label for="annee">Année* :</label>
            <input type="number" id="annee" name="annee" min="1900" max="<?= date('Y') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="couleur">Couleur* :</label>
            <input type="text" id="couleur" name="couleur" required>
        </div>
        
        <div class="form-group">
            <label for="kilometrage">Kilométrage* :</label>
            <input type="number" id="kilometrage" name="kilometrage" min="0" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description* :</label>
            <textarea id="description" name="description" rows="5" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="photo">Photo :</label>
            <input type="file" id="photo" name="photo" accept="image/*">
        </div>
        
        <button type="submit">Publier l'annonce</button>
    </form>
</body>
</html>