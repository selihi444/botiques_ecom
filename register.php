<?php
session_start();

try {
    $conn = new PDO("mysql:host=localhost;dbname=vente_etudiants", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            // Insérer l'utilisateur
            $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, rolee) 
                                    VALUES (:nom, :email, :mot_de_passe, :rolee)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':mot_de_passe', $mot_de_passe);
            $stmt->bindParam(':rolee', $role);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Inscription réussie. Vous pouvez vous connecter.";
                header("Location: login.php");
                exit();
            } else {
                $erreur = "Erreur lors de l'inscription.";
            }
        }
    }

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #fff;
            width: 400px;
            margin: 50px auto;
            padding: 30px 40px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        
        h2 {
            text-align: center;
            color: #444;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #f9f9f9;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #666;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #444;
        }

        p {
            text-align: center;
            margin-top: 15px;
            color: #666;
        }

        a {
            color: #444;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Créer un compte</h2>

    <?php if (isset($erreur)) echo "<p class='error'>$erreur</p>"; ?>

    <form method="post" action="">
        <label>Nom :</label>
        <input type="text" name="nom" required>

        <label>Email :</label>
        <input type="email" name="email" required>

        <label>Mot de passe :</label>
        <input type="password" name="mot_de_passe" required>

        <label>Rôle :</label>
        <select name="role" required>
            <option value="acheteur">Acheteur</option>
            <option value="vendeur">Vendeur</option>
        </select>

        <input type="submit" value="S'inscrire">
    </form>

    <p>Déjà inscrit ? <a href="login.php">Se connecter ici</a></p>
</div>

</body>
</html>
