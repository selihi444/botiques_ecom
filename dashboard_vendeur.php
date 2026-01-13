
<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'vendeur') {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=vente_etudiants", "root", "amal");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_vendeur = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT * FROM produits WHERE vendeur_id = :id_vendeur");
    $stmt->bindParam(':id_vendeur', $id_vendeur, PDO::PARAM_INT);
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
    <title>Dashboard Vendeur</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff0f5;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color:rgb(229, 97, 163)
        }

        nav {
            background-color: #ff80ab;
            padding: 15px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        h2 {
            text-align: center;
            color: #d63384;
            margin-top: 20px;
        }

        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .product-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .product-info {
            padding: 15px;
            flex-grow: 1;
        }

        .product-info h4 {
            margin: 0 0 10px;
            color: #d63384;
        }

        .product-info p {
            margin: 5px 0;
        }

        .product-actions {
            display: flex;
            
            justify-content: space-between;
            padding: 10px 15px;
            background-color: #ffe6f0;
        }

        .product-actions a {
            color: #d63384;
            font-weight: bold;
            text-decoration: none;
        }

        .product-actions a:hover {
            text-decoration: underline;
        }

        .add-product {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #ff99bb;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        .add-product:hover {
            background-color: #ff7aab;
        }

        footer {
            text-align: center;
            padding: 15px;
            background-color: #ffc0cb;
            color: #7a003c;
            margin-top: 40px;
        }
        .logout-link {
            text-align: right;
            padding: 10px 20px;
        }

    </style>
</head>
<body>
<header>
    Bienvenue sur la Plateforme de Vente Étudiants
</header>

<nav>
    <a href="dashboard_vendeur.php">Accueil</a>
    <a href="produits.php">Produits</a>
    <a href="mon_compte.php">Mon Compte</a>
    <a href="panier.php">🛒 Panier</a>
    <a href="ajouter_produit.php">+ Ajouter Produit</a>
    <a href="logout.php">Déconnexion</a>
</nav>

<div class="logout-link">
    <h2>Bienvenue, <?php echo $_SESSION['nom']; ?> (Vendeur)</h2>
</div>

<div id="produits" class="product-container">
    <?php if (!empty($produits)): ?>
        <?php foreach ($produits as $row): ?>
            <div class="product-card">
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Produit">
                <div class="product-info">
                    <h4><?php echo htmlspecialchars($row['nom']); ?></h4>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <p><strong><?php echo htmlspecialchars($row['prix']); ?> DH</strong></p>
                    <p>Quantité : <?php echo htmlspecialchars($row['quantite']); ?></p>
                </div>
                <div class="product-actions">
                    <a href="modifier_produit.php?id=<?php echo $row['id']; ?>">Modifier</a>
                    <a href="supprimer_produit.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center;">Vous n'avez pas encore ajouté de produits.</p>
    <?php endif; ?>
</div>

<footer>
    &copy; 2025 Plateforme de Vente Étudiants - Tous droits réservés.
</footer>

</body>
</html>
