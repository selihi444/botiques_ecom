
<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'acheteur') {
    header("Location:login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "amal", "vente_etudiants");
if ($conn->connect_error) {
    die("❌ Erreur de connexion : " . $conn->connect_error);
}

$id_acheteur = intval($_SESSION['id']);

$sql = "SELECT p.nom, p.description, p.prix, p.image, pa.quantite
        FROM panier pa
        JOIN produits p ON pa.produit_id = p.id
        WHERE pa.utilisateur_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_acheteur);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
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
            padding: 20px 0;
            text-align: center;
            font-size: 24px;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .links {
            text-align: center;
            margin-bottom: 20px;
        }

        .links a {
            color: #e561a3;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }

        .links a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
        }

        input[type="submit"] {
            background-color: rgb(211, 82, 198);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            float: right;
        }

        input[type="submit"]:hover {
            background-color: rgb(198, 85, 200);
        }

        .empty {
            text-align: center;
            font-size: 18px;
            margin-top: 40px;
        }

        footer {
            background-color: #333;
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<header>🛒 Mon Panier</header>

<div class="container">
    <div class="links">
        <a href="dashboard_acheteur.php">⬅ Retour au Dashboard</a> |
        <a href="logout.php">Déconnexion</a>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <form action="valider_commande.php" method="post">
            <table>
                <tr>
                    <th>Produit</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Image</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nom']); ?></td>
                        <td><?= htmlspecialchars($row['description']); ?></td>
                        <td><?= number_format($row['prix'], 2); ?> €</td>
                        <td><?= intval($row['quantite']); ?></td>
                        <td>
                            <img src="uploads/<?= htmlspecialchars($row['image']); ?>" alt="Image produit">
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
            <input type="submit" value="Commander">
        </form>
    <?php else: ?>
        <p class="empty">Votre panier est vide.</p>
    <?php endif; ?>
</div>

<footer>
    &copy; 2025 Plateforme de Vente Étudiants - Tous droits réservés.
</footer>

</body>
</html>












