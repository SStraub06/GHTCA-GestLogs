<?php
session_start();

include "../config/database.php";
include "../includes/security.php";
include "../includes/header.php";

// 🔒 filtre automatique (IMPORTANT)
$filter_poste = str_replace("WHERE", "AND", getSiteFilter("p"));
$filter_conn  = str_replace("WHERE", "AND", getConnexionFilter("c", "p"));

// 🔒 Si l'utilisateur n'est pas admin → site forcé
if (!$_SESSION['is_admin']) {
    $_GET['site'] = $_SESSION['site_id'];
}

$site = $_GET['site'] ?? '';

$sql = "
SELECT p.*, s.nom as site_nom
FROM postes p
JOIN sites s ON p.id_site = s.id
WHERE 1=1
$filter_poste
";

$params = [];

if (!empty($site)) {
    $sql .= " AND s.id = :site";
    $params['site'] = $site;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$postes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Postes</title>
    <style>
        body { font-family: Arial; background:#f5f6fa; }
        table { border-collapse: collapse; width:100%; background:white; }
        th, td { padding:10px; border:1px solid #ddd; }
        th { background:#2f3640; color:white; }
        a { text-decoration:none; color:#0984e3; }
        .container { width:90%; margin:auto; margin-top:30px; }

        /* Ajout pour respecter l’indentation visuelle */
        .filter-card {
            background:white;
            padding:15px;
            border-radius:10px;
            box-shadow:0 4px 10px rgba(0,0,0,0.1);
            margin-bottom:20px;
            width: fit-content;
        }

        .filter-form {
            display:flex;
            align-items:center;
            gap:10px;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Liste des postes</h2>

    <!-- 🔹 Carte filtre ajoutée -->
    <div class="filter-card">
        <form method="GET" class="filter-form">

            <select name="site" <?= !$_SESSION['is_admin'] ? 'disabled' : '' ?>>
                <option value="">Tous les sites</option>
                <?php
                $sites = $pdo->query("SELECT * FROM sites")->fetchAll();
                foreach ($sites as $s) {
                    echo "<option value='{$s['id']}'>{$s['nom']}</option>";
                }
                ?>
            </select>

            <?php if (!$_SESSION['is_admin']): ?>
                <input type="hidden" name="site" value="<?= $_SESSION['site_id'] ?>">
            <?php endif; ?>

            <button type="submit">Filtrer</button>
        </form>
    </div>

    <br>

    <!-- Données BDD Tableau liste -->
    <table>
        <tr>
            <th>Nom poste</th>
            <th>MAC</th>
            <th>OS</th>
            <th>Site</th>
            <th>Action</th>
        </tr>

        <?php foreach ($postes as $p): ?>
        <tr>
            <td><?= $p['nom_poste'] ?></td>
            <td><?= $p['adresse_mac'] ?></td>
            <td><?= $p['os_version'] ?></td>
            <td><?= $p['site_nom'] ?></td>
            <td>
                <a href="poste_detail.php?id=<?= $p['id'] ?>">Voir détails</a>
            </td>
        </tr>
        <?php endforeach; ?>

    </table>
</div>

<style>

.title {
    text-align: center;
    margin-bottom: 20px;
    color: #2f3640;
}

/* 🔹 Carte filtre */
.filter-card {
    width: 90%;
    margin: auto;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

/* 🔹 Formulaire */
.filter-form {
    display: flex;
    align-items: flex-end;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 5px;
    color: #2f3640;
    font-weight: bold;
}

select {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    min-width: 200px;
}

/* 🔹 Bouton */
button {
    background: #0984e3;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #74b9ff;
}

</style>

</body>
</html>
