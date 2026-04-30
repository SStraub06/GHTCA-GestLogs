<?php
session_start();

require "../config/database.php";
require "../includes/security.php";

// 🔒 filtres automatiques AD
$filter_poste = str_replace("WHERE", "AND", getSiteFilter("p"));
$filter_conn  = str_replace("WHERE", "AND", getConnexionFilter("c", "p"));

$search = $_POST['search'] ?? '';
$site = $_POST['site'] ?? '';
$date_debut = $_POST['date_debut'] ?? '';
$date_fin = $_POST['date_fin'] ?? '';

$sql = "

SELECT
p.id,
p.nom_poste,
p.adresse_mac,
c.nom_utilisateur,
c.adresse_ip,
c.date_connexion

FROM connexions c
JOIN postes p ON c.id_poste = p.id
JOIN sites s ON p.id_site = s.id

WHERE 1=1
$filter_conn
";

$params = [];

if($search){

    $sql .= " AND (
        p.nom_poste LIKE :search
        OR c.nom_utilisateur LIKE :search
        OR c.adresse_ip LIKE :search
        OR p.adresse_mac LIKE :search
    )";

    $params['search'] = "%$search%";
}

if($site){
    $sql .= " AND s.id = :site";
    $params['site'] = $site;
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

while($row = $stmt->fetch()){

    echo "<tr>";

    echo "<td>
    <a href='../pages/poste_detail.php?id=".$row['id']."&search=".urlencode($search)."&site=".$site."'>
    ".$row['nom_poste']."
    </a>
    </td>";

    // Info-bulle AD ici
    echo "<td>
        <span class='ad-user' data-user='".htmlspecialchars($row['nom_utilisateur'])."'>
            ".htmlspecialchars($row['nom_utilisateur'])."
        </span>
    </td>";
    echo "<td>".$row['adresse_ip']."</td>";
    echo "<td>".$row['adresse_mac']."</td>";
    echo "<td>".$row['date_connexion']."</td>";

    echo "</tr>";
}
