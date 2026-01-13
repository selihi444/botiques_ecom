
<?php
session_start();

// vérifier si l'utilisateur est connecté et est vendeur
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'vendeur') {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=vente_etudiants", "root", "amal");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$id_produit = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_vendeur = $_SESSION['id'];

// récupérer les données du produit
$stmt = $conn->prepare("SELECT * FROM produits WHERE id = :id AND vendeur_id = :vendeur_id");
$stmt->execute(['id' => $id_produit, 'vendeur_id' => $id_vendeur]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    echo "Produit introuvable.";
    exit();
}

// traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = floatval($_POST['prix']);
    $quantite = intval($_POST['quantite']);

    // gérer l'image si elle a été modifiée
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($image_tmp, "uploads/" . $image);
    } else {
        $image = $produit['image'];
    }

    // mettre à jour le produit
    $sql_update = "UPDATE produits SET nom = :nom, description = :description, prix = :prix,
                   quantite = :quantite, image = :image
                   WHERE id = :id AND vendeur_id = :vendeur_id";

    $stmt_update = $conn->prepare($sql_update);

    try {
        $stmt_update->execute([
            'nom' => $nom,
            'description' => $description,
            'prix' => $prix,
            'quantite' => $quantite,
            'image' => $image,
            'id' => $id_produit,
            'vendeur_id' => $id_vendeur
        ]);
        header("Location: dashboard_vendeur.php");
        exit();
    } catch (PDOException $e) {
        $erreur = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le produit</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffe6f0;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #d63384;
            text-align: center;
        }

        form {
            background-color: #fff0f5;
            border: 2px solid #f8c0d8;
            border-radius: 12px;
            padding: 30px;
            max-width: 600px;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(214, 51, 132, 0.2);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #c2185b;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5a9c3;
            border-radius: 8px;
            background-color: #fff;
        }

        input[type="submit"] {
            background-color: #e91e63;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #d81b60;
        }

        img {
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        p a {
            display: inline-block;
            margin-top: 20px;
            color: #e91e63;
            text-decoration: none;
            font-weight: bold;
        }

        p a:hover {
            text-decoration: underline;
        }

        p {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Modifier le produit</h2>
    <?php if (isset($erreur)) echo "<p style='color:red; text-align:center;'>$erreur</p>"; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Nom :</label>
        <input type="text" name="nom" value="<?php echo htmlspecialchars($produit['nom']); ?>" required>

        <label>Description :</label>
        <textarea name="description" required><?php echo htmlspecialchars($produit['description']); ?></textarea>

        <label>Prix (Mad) :</label>
        <input type="number" name="prix" step="0.01" value="<?php echo $produit['prix']; ?>" required>

        <label>Quantité :</label>
        <input type="number" name="quantite" value="<?php echo $produit['quantite']; ?>" required>

        <label>Image actuelle :</label><br>
        <img src="uploads/<?php echo $produit['image']; ?>" width="100"><br><br>

        <label>Nouvelle image (optionnelle) :</label>
        <input type="file" name="image" accept="image/*">

        <input type="submit" value="Enregistrer les modifications">
    </form>

    <p><a href="dashboard_vendeur.php">Retour au tableau de bord</a></p>
</body>
</html>