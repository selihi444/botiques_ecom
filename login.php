<?php
session_start();

try {
    $conn = new PDO("mysql:host=localhost;dbname=vente_etudiants", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
                $_SESSION['id'] = $utilisateur['id'];
                $_SESSION['nom'] = $utilisateur['nom'];
                $_SESSION['role'] = $utilisateur['rolee'];

                if ($utilisateur['rolee'] == 'vendeur') {
                    header("Location: dashboard_vendeur.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $erreur = "Mot de passe incorrect.";
            }
        } else {
            $erreur = "Email non trouvé ou compte désactivé.";
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
    <title>Connexion</title>
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

        input[type="email"],
        input[type="password"] {
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

        .success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Se connecter</h2>

    <?php if (isset($erreur)) echo "<p class='error'>$erreur</p>"; ?>
    <?php if (isset($_SESSION['message'])) { echo "<p class='success'>".$_SESSION['message']."</p>"; unset($_SESSION['message']); } ?>

    <form method="post" action="">
        <label>Email :</label>
        <input type="email" name="email" required>

        <label>Mot de passe :</label>
        <input type="password" name="mot_de_passe" required>

        <input type="submit" value="Se connecter">
    </form>

    <p>Pas encore de compte ? <a href="register.php">Créer un compte ici</a></p>
</div>

</body>
</html>