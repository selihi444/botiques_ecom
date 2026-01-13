
<?php
session_start();

// ✅ التحقق من تسجيل الدخول والدور
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'acheteur') {
    header("Location: ../login.php");
    exit();
}

try {
    // ✅ الاتصال بقاعدة البيانات باستخدام PDO
    $pdo = new PDO("mysql:host=localhost;dbname=vente_etudiants;charset=utf8", "root", "amal");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $acheteur_id = $_SESSION['id'];

    // ✅ التحضير والاستعلام
    $stmt = $pdo->prepare("SELECT p.nom AS nom_produit, p.prix, c.quantite, c.date_commande
                           FROM commandes c
                           JOIN produits p ON c.produit_id = p.id
                           WHERE c.acheteur_id = ?");
                               $stmt->execute([$acheteur_id]);

    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion ou requête : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
</head>
<body>
    <h2>Mes Commandes</h2>
    <p><a href="dashboard_acheteur.php">⬅ Retour au dashboard</a> | <a href="../logout.php">Déconnexion</a></p>

    <?php if (!empty($commandes)): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Produit</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Date</th>
            </tr>
            <?php foreach ($commandes as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nom_produit']); ?></td>
                <td><?php echo number_format($row['prix'], 2); ?> €</td>
                <td><?php echo (int)$row['quantite']; ?></td>
                <td><?php echo htmlspecialchars($row['date_commande']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Vous n'avez passé aucune commande.</p>
    <?php endif; ?>
</body>
</html>