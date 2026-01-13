<?php
session_start();


if (!isset($_SESSION['id']) || $_SESSION['role'] != 'vendeur') {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=vente_etudiants", "root", "amal");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}


$id_vendeur = $_SESSION['id'];
$id_produit = isset($_GET['id']) ? intval($_GET['id']) : 0;


$sql_check = "SELECT image FROM produits WHERE id = :id AND vendeur_id = :vendeur_id";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->execute([
    'id' => $id_produit,
    'vendeur_id' => $id_vendeur
]);

if ($stmt_check->rowCount() != 1) {
    echo "Produit introuvable ou vous n'avez pas la permission.";
    exit();
}

$produit = $stmt_check->fetch(PDO::FETCH_ASSOC);
$image_path = "uploads/" . $produit['image'];


$sql_delete = "DELETE FROM produits WHERE id = :id AND vendeur_id = :vendeur_id";
$stmt_delete = $conn->prepare($sql_delete);

try {
    $stmt_delete->execute([
        'id' => $id_produit,
        'vendeur_id' => $id_vendeur
    ]);


    if (file_exists($image_path)) {
        unlink($image_path);
    }

    
    header("Location: dashboard_vendeur.php");
    exit();
} catch (PDOException $e) {
    echo "Erreur lors de la suppression : " . $e->getMessage();
}
?>