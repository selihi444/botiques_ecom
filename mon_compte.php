<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=vente_etudiants;charset=utf8", "root", "amal");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_SESSION['id'];
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $message = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'];
        $email = $_POST['email'];

        $update_stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, email = ? WHERE id = ?");
        if ($update_stmt->execute([$nom, $email, $id])) {
            $_SESSION['nom'] = $nom;
            $user['nom'] = $nom;
            $user['email'] = $email;
            $message = "<p class='success'>Informations mises à jour avec succès.</p>";
        } else {
            $message = "<p class='error'>Erreur lors de la mise à jour.</p>";
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
    <title>Mon Compte</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff0f5;
            margin: 0;
            padding: 0;
            color: #444;
        }

        header {
            background-color: #ffffff;
            color: rgb(229, 97, 163);
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        form input[type="submit"] {
            background-color: rgb(219, 129, 210);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        form input[type="submit"]:hover {
            background-color: rgb(226, 89, 223);
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .links {
            text-align: center;
            margin-bottom: 20px;
        }

        .links a {
            color: #ffc0cb;
            text-decoration: none;
            margin: 0 10px;
        }

        .links a:hover {
            text-decoration: underline;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<header>Mon Compte</header>

<div class="container">
    <div class="links">
        <a href="<?= $_SESSION['role'] == 'vendeur' ? 'dashboard_vendeur.php' : 'dashboard_acheteur.php'; ?>">⬅ Retour</a>
        
        <a href="logout.php">Déconnexion</a>
    </div>

    <h2>Modifier mes informations</h2>

    <div class="message"><?= $message ?></div>

    <form method="POST">
        <label>Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <input type="submit" value="Mettre à jour">
    </form>
</div>

<footer>
    &copy; 2025 Plateforme de Vente Étudiants - Tous droits réservés.
</footer>

</body>
</html>