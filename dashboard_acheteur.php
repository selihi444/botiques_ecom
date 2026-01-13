
<?php
session_start();


if (!isset($_SESSION['id']) || $_SESSION['role'] != 'acheteur') {
    header("Location:login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=vente_etudiants", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $sql = "SELECT p.*, v.nom AS nom_vendeur 
            FROM produits p 
            JOIN utilisateurs v ON p.vendeur_id = v.id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion ou de requête : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Acheteur</title>
    <style>
       
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #fff0f5; color: #444; }
        header { background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px; text-align: center; font-size: 24px; font-weight: bold; color:rgb(229, 97, 163); }
        nav { background-color: #ffc0cb; display: flex; justify-content: center; gap: 20px; padding: 15px 0; }
        nav a { color: #fff; text-decoration: none; font-weight: 500; }
        nav a:hover { text-decoration: underline; background-color: #ff69b4; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .product-card { background-color: white; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); padding: 15px; display: flex; flex-direction: column; align-items: center; transition: transform 0.2s ease; }
        .product-card:hover { transform: scale(1.03); }
        .product-card img { width: 100%; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
        .product-card h4 { margin: 10px 0 5px; font-size: 18px; color: #222; }
        .product-card .price { font-size: 16px; color:rgb(205, 79, 155); font-weight: bold; }
        .product-card .quantity { color: #777; margin: 5px 0; }
        .product-card .vendeur { font-size: 14px; color: #555; }
        .product-card a { margin-top: 10px; display: inline-block; background-color: #ff69b4; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; }
        .product-card a:hover { background-color: #ff69b4; }
        footer { margin-top: 40px; background-color: #333; color: white; text-align: center; padding: 20px; }
        .logout-link { text-align: right; padding: 10px 20px; }
        .logout-link a { color: #ffc0cb; text-decoration: none; }
        .logout-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<header>
    Bienvenue sur la Plateforme de Vente Étudiants
</header>

<nav>
    <a href="dashboard_acheteur.php">Accueil</a>
    <a href="produits.php">Produits</a>
    <a href="mon_compte.php">Mon Compte</a>
    <a href="panier.php">🛒 Panier</a>
    <a href="logout.php">Déconnexion</a>
</nav>

<div class="container">
    <div class="logout-link">
        Bonjour, <?php echo htmlspecialchars($_SESSION['nom']); ?> | <a href="../logout.php">Déconnexion</a>
    </div>

    <h2>Produits disponibles</h2>

    <?php if ($produits && count($produits) > 0): ?>
        <div class="product-grid">
            <?php foreach ($produits as $row): ?>
                <div class="product-card">
                    <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Image produit" >
                    <h4><?php echo htmlspecialchars($row['nom']); ?></h4>
                    <div class="price"><?php echo $row['prix']; ?> DH</div>
                    <div class="quantity">Quantité: <?php echo $row['quantite']; ?></div>
                    <div class="vendeur">Vendu par: <?php echo htmlspecialchars($row['nom_vendeur']); ?></div>
                    <a href="ajouter_panier.php?id_produit=<?php echo $row['id']; ?>">Ajouter au panier</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Aucun produit disponible pour le moment.</p>
    <?php endif; ?>

</div>

<footer>
    &copy; 2025 Plateforme de Vente Étudiants - Tous droits réservés.
</footer>

</body>
</html>

