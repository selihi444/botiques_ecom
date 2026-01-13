<?php
session_start();

// التوجيه حسب الدور
if (isset($_SESSION['id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'vendeur') {
        header("Location: dashboard_vendeur.php");
        exit();
    } elseif ($_SESSION['role'] == 'acheteur') {
        header("Location: dashboard_acheteur.php");
        exit();
    }
} else {
    // المستخدم غير مسجل الدخول
    header("Location: login.php");
    exit();
}