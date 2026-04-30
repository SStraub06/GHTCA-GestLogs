<?php
session_start();

include "../includes/header.php";
require_once "../config/database.php";
require_once "../includes/security.php";

// 🔒 Si l'utilisateur n'est pas admin → site forcé
if (!$_SESSION['is_admin']) {
    $_GET['site'] = $_SESSION['site_id'];
}

// filtres automatiques
$filter_poste = str_replace("WHERE", "AND", getSiteFilter("p"));
$filter_conn  = str_replace("WHERE", "AND", getConnexionFilter("c", "p"));

$site = $_GET['site'] ?? '';
$user = $_GET['user'] ?? '';
$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';
?>

<style>

.container { width:90%; margin:auto; margin-top:30px; }

/* ----------------------------
   CARD / FILTRES
---------------------------- */
.card {
    background:white;
    padding:20px;
    margin-bottom:20px;
    border-radius:10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* FORM */
.filters {
    display:flex;
    gap:15px;
    flex-wrap:wrap;
    align-items:center;
}

.filters input, .filters select {
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
}

.filters button {
    padding:10px 15px;
    background:#3498db;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

.filters button:hover {
    background:#2980b9;
}

/* ----------------------------
   TABLE
---------------------------- */
.table-container {
    overflow-x:auto;
}

table {
    width:100%;
    border-collapse: collapse;
}

th {
    background:#2f3640;
    color:white;
    padding:10px;
}

td {
    padding:10px;
    border-bottom:1px solid #eee;
}

tr:hover {
    background:#f4f6f8;
}

/* LIENS */
a {
    text-decoration:none;
    color:#2c3e50;
}

a:hover {
    color:#3498db;
}

</style>

<div class="container">

<h2>Historique des connexions</h2>

<!-- 🔹 FILTRES -->
<div class="card">

<form method="GET" class="filters">

<!-- Site -->
<select name="site" <?= !$_SESSION['is_admin'] ? 'disabled' : '' ?>>
<option value="">Tous les sites</option>

<?php
$sites = $pdo->query("SELECT * FROM sites")->fetchAll();
foreach($sites as $s){
    $selected = ($site == $s['id']) ? "selected" : "";
    echo "<option value='{$s['id']}' $selected>{$s['nom']}</option>";
}
?>
</select>

<?php if (!$_SESSION['is_admin']): ?>
    <input type="hidden" name="site" value="<?= $_SESSION['site_id'] ?>">
<?php endif; ?>

<!-- Utilisateur -->
<input type="text" name="user" placeholder="Utilisateur"
value="<?= htmlspecialchars($user) ?>">

<!-- Date -->
<input type="date" name="date_debut" value="<?= $date_debut ?>">
<input type="date" name="date_fin" value="<?= $date_fin ?>">

<button type="submit">Filtrer</button>

</form>

</div>

<?php

// 🔹 Construction requête
$sql = "
SELECT c.*, p.nom_poste, s.nom as site_nom
FROM connexions c
JOIN postes p ON c.id_poste = p.id
JOIN sites s ON p.id_site = s.id
WHERE 1=1
$filter_conn
";

$params = [];

if($site){
    $sql .= " AND s.id = :site";
    $params['site'] = $site;
}

if($user){
    $sql .= " AND c.nom_utilisateur LIKE :user";
    $params['user'] = "%$user%";
}

if($date_debut){
    $sql .= " AND c.date_connexion >= :date_debut";
    $params['date_debut'] = $date_debut;
}

if($date_fin){
    $sql .= " AND c.date_connexion <= :date_fin";
    $params['date_fin'] = $date_fin;
}

$sql .= " ORDER BY c.date_connexion DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

?>

<!-- 🔹 TABLE -->
<div class="card">

<h3>Résultats</h3>

<div class="table-container">

<table>
<!-- Colonnes tableau principal -->
<tr>
<th>Poste</th>
<th>Utilisateur</th>
<th>IP</th>
<th>Site</th>
<th>Date connexion</th>
<th>Date déconnexion</th>
<th>Uptime</th>
<th>Imprimante</th>
</tr>

<?php while($row = $stmt->fetch()): ?>

<tr>
<!-- Données BDD Tableau connexions -->
<td>
<a href="poste_detail.php?id=<?= $row['id_poste'] ?>">
<?= $row['nom_poste'] ?>
</a>
</td>

<td>
    <span class="ad-user" data-user="<?= htmlspecialchars($row['nom_utilisateur']) ?>">
        <?= htmlspecialchars($row['nom_utilisateur']) ?>
    </span>
</td>

<td><?= $row['adresse_ip'] ?></td>
<td><?= $row['site_nom'] ?></td>
<td><?= $row['date_connexion'] ?></td>
<td><?= $row['date_deconnexion'] ?></td>
<td><?= $row['uptime'] ?></td>
<td><?= $row['imprimante_defaut'] ?></td>

</tr>

<?php endwhile; ?>

</table>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>
