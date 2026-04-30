<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <style>
        body {
            background:#f4f6f8;
            font-family: Arial;
        }

        .login-box {
            width:350px;
            margin:100px auto;
            background:white;
            padding:30px;
            border-radius:10px;
            box-shadow:0 4px 12px rgba(0,0,0,0.1);
            text-align:center;
        }

        h2 {
            margin-bottom:20px;
        }

        input {
            width:100%;
            padding:10px;
            margin-bottom:15px;
            border:1px solid #ccc;
            border-radius:6px;
        }

        button {
            width:100%;
            padding:10px;
            background:#3498db;
            color:white;
            border:none;
            border-radius:6px;
            cursor:pointer;
        }

        button:hover {
            background:#2980b9;
        }

        .error {
            color:red;
            margin-bottom:10px;
        }
    </style>
</head>

<body>

<div class="login-box">

<h2>Connexion</h2>

<?php
if(isset($_GET['error'])){
    echo "<div class='error'>Identifiants invalides</div>";
}
?>

<form method="POST" action="auth.php">

<input type="text" name="username" placeholder="Utilisateur" required>
<input type="password" name="password" placeholder="Mot de passe" required>

<button type="submit">Se connecter</button>

</form>

</div>

</body>
</html>