<?php

session_start();

include "../config/database.php";
include "../includes/security.php";
include "../includes/header.php";

// 🔒 filtre automatique (IMPORTANT)
$filter_poste = str_replace("WHERE", "AND", getSiteFilter("p"));
$filter_conn  = str_replace("WHERE", "AND", getConnexionFilter("c", "p"));

// 🔹 Nombre total de postes
$query = $pdo->query("
SELECT COUNT(*) as total 
FROM postes p
WHERE 1=1 $filter_poste
");
$total_postes = $query->fetch()['total'];

// 🔹 Nombre total de connexions
$query = $pdo->query("
SELECT COUNT(*) as total 
FROM connexions c
JOIN postes p ON c.id_poste = p.id
WHERE 1=1 $filter_conn
");
$total_connexions = $query->fetch()['total'];

// 🔹 Dernière connexion
$query = $pdo->query("
SELECT c.date_connexion
FROM connexions c
JOIN postes p ON c.id_poste = p.id
WHERE 1=1 $filter_conn
ORDER BY c.date_connexion DESC
LIMIT 1
");
$last_connexion = $query->fetchColumn();

// 🔹 Dernier utilisateur
$query = $pdo->query("
SELECT c.nom_utilisateur
FROM connexions c
JOIN postes p ON c.id_poste = p.id
WHERE 1=1 $filter_conn
ORDER BY c.date_connexion DESC
LIMIT 1
");
$last_user = $query->fetchColumn();

?>

<h1>Dashboard</h1>
<!-- Tableau cards -->
<div class="dashboard">

<div class="card">
<h3>Postes enregistrés</h3>
<p><?= $total_postes ?></p>
</div>

<div class="card">
<h3>Connexions enregistrées</h3>
<p><?= $total_connexions ?></p>
</div>

<div class="card">
<h3>Dernière connexion</h3>
<p><?= $last_connexion ?? "Aucune donnée" ?></p>
</div>

<div class="card">
<h3>Dernier utilisateur</h3>
<p>
<?php if ($last_user): ?>
    <span class="ad-user" data-user="<?= htmlspecialchars($last_user) ?>">
        <?= htmlspecialchars($last_user) ?>
    </span>
<?php else: ?>
    Aucune donnée
<?php endif; ?>
</p>
</div>

</div>

<hr>

<h2>Dernières connexions</h2>

<table>

<thead>
<tr>
<th>Poste</th>
<th>Utilisateur</th>
<th>Date connexion</th>
</tr>
</thead>

<tbody>

<?php

$query = $pdo->query("
SELECT p.id, p.nom_poste, c.nom_utilisateur, c.date_connexion
FROM connexions c
JOIN postes p ON c.id_poste = p.id
WHERE 1=1 $filter_conn
ORDER BY c.date_connexion DESC
LIMIT 10
");

while($row = $query->fetch()){

echo "<tr>";

echo "<td>
<a href='poste_detail.php?id=".$row['id']."'>
".$row['nom_poste']."
</a>
</td>";

echo "<td>
    <span class='ad-user' data-user='".htmlspecialchars($row['nom_utilisateur'])."'>
        ".htmlspecialchars($row['nom_utilisateur'])."
    </span>
</td>";

echo "<td>".$row['date_connexion']."</td>";

echo "</tr>";
}

?>

</tbody>

</table>

<?php include "../includes/footer.php"; ?>
<!-- Style CSS -->
<style>

h1 {
    text-align: center;
    color: #2f3640;
}

.dashboard {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    width: 90%;
    margin: 30px auto;
}

.card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.card h3 {
    margin-bottom: 10px;
}

.card p {
    font-size: 24px;
    font-weight: bold;
    color: #0984e3;
}

table {
    width: 90%;
    margin: auto;
    border-collapse: collapse;
    background: white;
}

th {
    background: #2f3640;
    color: white;
    padding: 12px;
}

td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

tr:hover {
    background: #f1f2f6;
}

a {
    color: #0984e3;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

</style>
