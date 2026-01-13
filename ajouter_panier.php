<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'acheteur') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id_produit']) || !is_numeric($_GET['id_produit'])) {
    die("Produit non spécifié ou invalide.");
}

$id_utilisateur = intval($_SESSION['id']);
$id_produit = intval($_GET['id_produit']);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=vente_etudiants;charset=utf8", "root", "amal");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // تحقق من وجود المنتج
    $stmt_produit = $pdo->prepare("SELECT id FROM produits WHERE id = ?");
    $stmt_produit->execute([$id_produit]);
    if ($stmt_produit->rowCount() == 0) {
        die("Produit introuvable.");
    }

    // التحقق من وجود المنتج في السلة
    $stmt_check = $pdo->prepare("SELECT quantite FROM panier WHERE utilisateur_id = ? AND produit_id = ?");
    $stmt_check->execute([$id_utilisateur, $id_produit]);

    if ($stmt_check->rowCount() == 0) {
        // المنتج غير موجود، نضيفه بكمية 1
        $stmt_insert = $pdo->prepare("INSERT INTO panier (utilisateur_id, produit_id, quantite) VALUES (?, ?, 1)");
        $stmt_insert->execute([$id_utilisateur, $id_produit]);
    } else {
        // المنتج موجود، نزيد الكمية بواحد
        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
        $nouvelle_quantite = $row['quantite'] + 1;

        // تحديث الكمية باستخدام معرف المستخدم ومعرف المنتج
        $stmt_update = $pdo->prepare("UPDATE panier SET quantite = ? WHERE utilisateur_id = ? AND produit_id = ?");
        $stmt_update->execute([$nouvelle_quantite, $id_utilisateur, $id_produit]);
    }

    header("Location: panier.php");
    exit();

} catch (PDOException $e) {
    die("Erreur de connexion ou requête : " . $e->getMessage());
}
?>