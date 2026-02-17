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

// Récupérer l'annonce à modifier
try {
    $stmt = $pdo->prepare("SELECT * FROM Annonces WHERE idannonce = ? AND idvendeur = ?");
    $stmt->execute([$idannonce, $_SESSION['idvendeur']]);
    $annonce = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$annonce) {
        header('Location: annonces.php');
        exit();
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération de l'annonce: " . $e->getMessage());
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    
    // Gestion de l'upload de photo
    $photo = $annonce['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Supprimer l'ancienne photo si elle existe
            if (!empty($photo) && file_exists('uploads/' . $photo)) {
                unlink('uploads/' . $photo);
            }
            
            $newName = uniqid('img_') . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $newName);
            $photo = $newName;
        }
    }
    
    // Mise à jour dans la base de données
    try {
        $stmt = $pdo->prepare("UPDATE Annonces SET titre = ?, description = ?, prix = ?, photo = ? WHERE idannonce = ?");
        $stmt->execute([$titre, $description, $prix, $photo, $idannonce]);
        
        $success = "Annonce modifiée avec succès!";
        // Rafraîchir les données de l'annonce
        $annonce = array_merge($annonce, [
            'titre' => $titre,
            'description' => $description,
            'prix' => $prix,
            'photo' => $photo
        ]);
    } catch (PDOException $e) {
        $error = "Erreur lors de la modification de l'annonce: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'annonce</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
        .error { color: red; }
        .success { color: green; }
        .current-photo {
            max-width: 200px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Modifier l'annonce</h1>
    
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    
    <form action="modifier_annonce.php?id=<?php echo $idannonce; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="titre">Titre de l'annonce:</label>
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($annonce['titre']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($annonce['description']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="prix">Prix (€):</label>
            <input type="number" id="prix" name="prix" step="0.01" value="<?php echo htmlspecialchars($annonce['prix']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Photo actuelle:</label>
            <?php if (!empty($annonce['photo'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($annonce['photo']); ?>" class="current-photo">
            <?php else: ?>
                <p>Aucune photo</p>
            <?php endif; ?>
            
            <label for="photo">Changer la photo:</label>
            <input type="file" id="photo" name="photo" accept="image/*">
        </div>
        
        <button type="submit">Enregistrer les modifications</button>
    </form>
    
    <p><a href="annonces.php">Retour à la liste des annonces</a></p>
</body>
</html>