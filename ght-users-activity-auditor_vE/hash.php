<form method="post">
    <input type="password" name="pwd" placeholder="Mot de passe">
    <button type="submit">Générer</button>
</form>

<?php
if (!empty($_POST['pwd'])) {
    echo "<pre>" . password_hash($_POST['pwd'], PASSWORD_DEFAULT) . "</pre>";
}
?>
