<?php
session_start();

// vérifier si l'utilisateur est un vendeur
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'vendeur') {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=vente_etudiants", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        $prix = floatval($_POST['prix']);
        $quantite = intval($_POST['quantite']);
        $id_vendeur = $_SESSION['id'];

        // gestion de l'image
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = "uploads/" . $image;

        if (move_uploaded_file($image_tmp, $image_path)) {
            $sql = "INSERT INTO produits (vendeur_id, nom, description, prix, quantite, image)
                    VALUES (:vendeur_id, :nom, :description, :prix, :quantite, :image)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':vendeur_id', $id_vendeur, PDO::PARAM_INT);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':prix', $prix);
            $stmt->bindParam(':quantite', $quantite, PDO::PARAM_INT);
            $stmt->bindParam(':image', $image);

            if ($stmt->execute()) {
                header("Location: dashboard_vendeur.php");
                exit();
            } else {
                $erreur = "Erreur lors de l'ajout du produit.";
            }
        } else {
            $erreur = "Échec de l'envoi de l'image.";
        }
    }
} catch (PDOException $e) {
    $erreur = "Erreur de connexion : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit</title>
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
    <h2>Ajouter un nouveau produit</h2>
    <?php if (isset($erreur)) echo "<p style='color:red; text-align:center;'>$erreur</p>"; ?>

    <form method="post" action="" enctype="multipart/form-data">
        <label>Nom du produit :</label>
        <input type="text" name="nom" required>

        <label>Description :</label>
        <textarea name="description" required></textarea>

        <label>Prix (Mad) :</label>
        <input type="number" name="prix" step="0.01" required>

        <label>Quantité :</label>
        <input type="number" name="quantite" required>

        <label>Image :</label>
        <input type="file" name="image" accept="image/*" required>

        <input type="submit" value="Ajouter le produit">
    </form>

    <p><a href="dashboard_vendeur.php">Retour au tableau de bord</a></p>
</body>

</html>