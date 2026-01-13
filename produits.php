<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=vente_etudiants;charset=utf8", "root", "amal");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $stmt = $pdo->prepare("SELECT p.*, u.nom AS nom_vendeur 
                           FROM produits p 
                           JOIN utilisateurs u ON p.vendeur_id = u.id");
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nos Produits</title>
    <style>
       

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .back-link {
            margin-bottom: 20px;
            display: inline-block;
            text-decoration: none;
            color: #ffc0cb;
            font-weight: bold;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .product-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .product-card:hover {
            transform: scale(1.02);
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-card h3 {
            margin: 10px 0 5px;
            color: #555;
        }

        .product-card .desc {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }

        .product-card .price {
            color: rgb(227, 74, 230);
            font-weight: bold;
            margin-bottom: 5px;
        }

        .product-card .vendeur {
            font-size: 13px;
            color: #555;
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

<header>Nos Produits</header>

<div class="container">
    <a class="back-link" href="dashboard_acheteur.php">← Retour au Dashboard</a>

    <?php if (!empty($produits)): ?>
        <div class="product-grid">
            <?php foreach ($produits as $row): ?>
                <div class="product-card">
                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Produit" >
                    <h3><?= htmlspecialchars($row['nom']) ?></h3>
                    <div class="desc"><?= htmlspecialchars($row['description']) ?></div>
                    <div class="price"><?= $row['prix'] ?> DH</div>
                    <div class="vendeur">Vendu par: <?= htmlspecialchars($row['nom_vendeur']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucun produit disponible.</p>
    <?php endif; ?>
</div>

<footer>
    &copy; 2025 Plateforme de Vente Étudiants - Tous droits réservés.
</footer>

</body>
</html>