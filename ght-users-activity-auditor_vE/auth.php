<?php
session_start();
require_once "config/database.php"; // contient la connexion MySQL

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header("Location: login.php?error=1");
    exit;
}

// 🔹 Vérifier l'utilisateur dans la base
$sql = "SELECT id, username, password_hash, site_id, is_admin 
        FROM users 
        WHERE username = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php?error=1");
    exit;
}

// 🔹 Vérifier le mot de passe
if (!password_verify($password, $user['password_hash'])) {
    header("Location: login.php?error=1");
    exit;
}

// 🔹 Auth OK → créer la session
$_SESSION['user'] = $user['username'];
$_SESSION['site_id'] = $user['site_id'];
$_SESSION['is_admin'] = (bool)$user['is_admin'];

header("Location: pages/dashboard.php");
exit;
