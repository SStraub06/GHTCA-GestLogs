<?php
session_start();

include "../config/database.php";
include "../includes/security.php";
include "../includes/header.php";

// filtre automatique (IMPORTANT)
$filter_poste = str_replace("WHERE", "AND", getSiteFilter("p"));
$filter_conn = str_replace("WHERE", "AND", getConnexionFilter("c", "p"));
?>

<style>

/* ----------------------------
   TITRE
---------------------------- */
.title {
    font-size: 26px;
    margin-bottom: 20px;
    color: #2c3e50;
}

/* ----------------------------
   CARDS GLOBAL
---------------------------- */
.stats-global {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.card {
    flex: 1;
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    text-align: center;
}

.card h3 {
    margin-bottom: 10px;
    color: #7f8c8d;
    font-size: 14px;
}

.card p {
    font-size: 28px;
    font-weight: bold;
    color: #2c3e50;
}

/* ----------------------------
   TABLE
---------------------------- */
.table-container {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #3498db;
    color: white;
    padding: 10px;
    text-align: left;
}

td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

tr:hover {
    background: #f4f6f8;
}

a {
    text-decoration: none;
    color: #2c3e50;
}

a:hover {
    color: #3498db;
}

/*  FILTRE DATE */
.filter-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.filter-bar input[type="date"] {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.filter-bar button {
    padding: 7px 14px;
    background: #2ecc71;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.2s;
}

.filter-bar button:hover {
    background: #27ae60;
}

/* ----------------------------
   RESPONSIVE
---------------------------- */
@media (max-width: 768px) {
    .stats-global {
        flex-direction: column;
    }
}

</style>

<h1 class="title">Statistiques</h1>

<form method="GET" class="filter-bar">
    <label>Du :</label>
    <input type="date" name="date_debut" value="<?= $_GET['date_debut'] ?? '' ?>">

    <label>Au :</label>
    <input type="date" name="date_fin" value="<?= $_GET['date_fin'] ?? '' ?>">

    <button type="submit">Filtrer</button>
</form>

<?php

$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';

//  Nombre total de postes (filtré AD)
$total_postes = $pdo->query("
    SELECT COUNT(*) 
    FROM postes p 
    WHERE 1=1 
    $filter_poste
")->fetchColumn();

// Nombre total de connexions (filtré AD)
$total_connexions = $pdo->query("
    SELECT COUNT(*) 
    FROM connexions c
    JOIN postes p ON c.id_poste = p.id
    WHERE 1=1
    $filter_conn
")->fetchColumn();

//  Dernière connexion (filtré AD)
$last_connexion = $pdo->query("
SELECT c.date_connexion 
FROM connexions c
JOIN postes p ON c.id_poste = p.id
WHERE 1=1
$filter_conn
ORDER BY c.date_connexion DESC 
LIMIT 1
")->fetchColumn();

//  Dernier utilisateur (filtré AD)
$last_user = $pdo->query("
SELECT c.nom_utilisateur 
FROM connexions c
JOIN postes p ON c.id_poste = p.id
WHERE 1=1
$filter_conn
ORDER BY c.date_connexion DESC 
LIMIT 1
")->fetchColumn();

$where_date = "";

if($date_debut && $date_fin){
    $where_date = "WHERE c.date_connexion BETWEEN :debut AND :fin";
}

?>

<!-- CARDS -->
<div class="stats-global">

    <div class="card">
        <h3>Postes</h3>
        <p><?= $total_postes ?></p>
    </div>

    <div class="card">
        <h3>Connexions</h3>
        <p><?= $total_connexions ?></p>
    </div>

    <div class="card">
        <h3>Dernière connexion</h3>
        <p><?= $last_connexion ?? "N/A" ?></p>
    </div>

    <div class="card">
        <h3>Dernier utilisateur</h3>
        <p><?= $last_user ?? "N/A" ?></p>
    </div>

</div>

<!-- CONNEXIONS PAR SITE -->
<div class="table-container">

<h2>Connexions par site</h2>

<table>

<thead>
<tr>
<th>Site</th>
<th>Nombre de connexions</th>
</tr>
</thead>

<tbody>

<?php

$sql = "
SELECT s.nom, COUNT(c.id) as total
FROM connexions c
JOIN postes p ON c.id_poste = p.id
JOIN sites s ON p.id_site = s.id
$where_date
$filter_conn
GROUP BY s.nom
ORDER BY total DESC
";

$stmt = $pdo->prepare($sql);

if($where_date){
    $stmt->execute([
        'debut' => $date_debut." 00:00:00",
        'fin' => $date_fin." 23:59:59"
    ]);
}else{
    $stmt->execute();
}

while ($row = $stmt->fetch()) {
    echo "<tr>";
    echo "<td>".$row['nom']."</td>";
    echo "<td>".$row['total']."</td>";
    echo "</tr>";
}

?>

</tbody>

</table>

</div>

<br>

<!-- TOP POSTES -->
<div class="table-container">

<h2>Postes les plus utilisés</h2>

<table>

<thead>
<tr>
<th>Poste</th>
<th>Connexions</th>
</tr>
</thead>

<tbody>

<?php

$sql = "
SELECT p.id, p.nom_poste, COUNT(c.id) as total
FROM connexions c
JOIN postes p ON c.id_poste = p.id
$where_date
$filter_conn
GROUP BY p.id
ORDER BY total DESC
LIMIT 10
";

$stmt = $pdo->prepare($sql);

if($where_date){
    $stmt->execute([
        'debut' => $date_debut." 00:00:00",
        'fin' => $date_fin." 23:59:59"
    ]);
}else{
    $stmt->execute();
}

while ($row = $stmt->fetch()) {

echo "<tr>";

echo "<td>
<a href='poste_detail.php?id=".$row['id']."'>
".$row['nom_poste']."
</a>
</td>";

echo "<td>".$row['total']."</td>";

echo "</tr>";

}

?>

</tbody>

</table>

</div>

<br>

<!-- POSTES PAR SITE -->
<div class="table-container">

<h2>Postes par site</h2>

<table>

<thead>
<tr>
<th>Site</th>
<th>Nombre de postes</th>
</tr>
</thead>

<tbody>

<?php

$sql = "
SELECT s.nom, COUNT(p.id) as total
FROM postes p
JOIN sites s ON p.id_site = s.id
WHERE 1=1
$filter_poste
GROUP BY s.nom
ORDER BY total DESC
";

$stmt = $pdo->query($sql);

while($row = $stmt->fetch()){
    echo "<tr>";
    echo "<td>".$row['nom']."</td>";
    echo "<td>".$row['total']."</td>";
    echo "</tr>";
}

?>

</tbody>

</table>

</div>

<br>

<!-- UTILISATEURS UNIQUES -->
<div class="table-container">

<h2>Utilisateurs uniques par site</h2>

<table>

<thead>
<tr>
<th>Site</th>
<th>Nombre d'utilisateurs uniques</th>
</tr>
</thead>

<tbody>

<?php

$sql = "
SELECT s.nom, COUNT(DISTINCT c.nom_utilisateur) as total
FROM connexions c
JOIN postes p ON c.id_poste = p.id
JOIN sites s ON p.id_site = s.id
$where_date
$filter_conn
GROUP BY s.nom
ORDER BY total DESC
";

$stmt = $pdo->prepare($sql);

if($where_date){
    $stmt->execute([
        'debut' => $date_debut." 00:00:00",
        'fin' => $date_fin." 23:59:59"
    ]);
}else{
    $stmt->execute();
}

while($row = $stmt->fetch()){
    echo "<tr>";
    echo "<td>".$row['nom']."</td>";
    echo "<td>".$row['total']."</td>";
    echo "</tr>";
}

?>

</tbody>

</table>

</div>

<?php include "../includes/footer.php"; ?>
