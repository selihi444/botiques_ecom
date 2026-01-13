
<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'acheteur') {
    header("Location: login.php");
    exit();
}

$message = "";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=vente_etudiants;charset=utf8", "root", "amal");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $acheteur_id = $_SESSION['id'];


    $stmt = $pdo->prepare("
        SELECT p.id AS produit_id, p.prix, pa.quantite
        FROM panier pa
        JOIN produits p ON pa.produit_id = p.id
        WHERE pa.utilisateur_id = ?
    ");
    $stmt->execute([$acheteur_id]);
    $panier_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($panier_items && count($panier_items) > 0) {
        $montant_total = 0;

        
        foreach ($panier_items as $item) {
            $montant_total += $item['prix'] * $item['quantite'];
        }

        $insert_cmd = $pdo->prepare("
            INSERT INTO commandes (acheteur_id, montant_total, date_commande)
            VALUES (?, ?, NOW())
        ");
        if ($insert_cmd->execute([$acheteur_id, $montant_total])) {
            $commande_id = $pdo->lastInsertId();


            foreach ($panier_items as $item) {
                $update_produit = $pdo->prepare("
                    UPDATE produits SET quantite = quantite - ? WHERE id = ?
                ");
                $update_produit->execute([$item['quantite'], $item['produit_id']]);
            }

            
            $clear_panier = $pdo->prepare("DELETE FROM panier WHERE utilisateur_id = ?");
            $clear_panier->execute([$acheteur_id]);

            $message = "<p class='success'>✅ Commande validée avec succès !</p>";
        } else {
            $message = "<p class='error'>❌ Erreur lors de l'enregistrement de la commande.</p>";
        }
    } else {
        $message = "<p class='error'>❌ Votre panier est vide.</p>";
    }

} catch (PDOException $e) {
    $message = "<p class='error'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Valider Commande</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f9;
            color: #444;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            color: #c71585;
            margin-bottom: 20px;
        }

        .success {
            color: green;
            font-size: 18px;
            margin: 20px 0;
        }

        .error {
            color: red;
            font-size: 18px;
            margin: 20px 0;
        }

        a {
            text-decoration: none;
            color: #fff;
            background-color: #c71585;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }

        a:hover {
            background-color: #a3146d;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Validation de la Commande</h2>
    <?= $message ?>
    <a href="dashboard_acheteur.php">⬅ Retour au tableau de bord</a>
</div>

</body>
</html>