<?php
session_start();

include "../config/database.php";
include "../includes/security.php";
include "../includes/header.php";

/* ---------------------------------------------------------
   VERROUILLAGE AUTOMATIQUE DU SITE SELON LE GROUPE AD
--------------------------------------------------------- */
if (!$_SESSION['is_admin']) {
    $_GET['site'] = $_SESSION['site_id']; // force le site
}

// filtre automatique (IMPORTANT)
$filter_poste = str_replace("WHERE", "AND", getSiteFilter("p"));
$filter_conn  = str_replace("WHERE", "AND", getConnexionFilter("c", "p"));

$type = $_GET['type'] ?? '';
?>

<style>

.container { width:90%; margin:auto; margin-top:30px; }

.card {
    background:white;
    padding:20px;
    margin-bottom:20px;
    border-radius:10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/*  SWITCH */
.switch {
    display:flex;
    gap:10px;
    margin-bottom:20px;
}

.switch a {
    padding:10px 20px;
    border-radius:8px;
    text-decoration:none;
    background:#ecf0f1;
    color:#2c3e50;
    font-weight:bold;
}

.switch a.active {
    background:#3498db;
    color:white;
}

/*  FILTRES */
.filters {
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:15px;
}

.filters input, .filters select {
    padding:8px;
    border-radius:6px;
    border:1px solid #ccc;
}

/*  CHECKBOX GROUP AMÉLIORÉ */
.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 10px;
    margin-bottom: 15px;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f4f6f8;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    transition: 0.2s;
    white-space: normal;
}

.checkbox-group label:hover {
    background: #eaf2f8;
    transform: translateY(-1px);
}

.checkbox-group input[type="checkbox"] {
    accent-color: #3498db;
    transform: scale(1.1);
}

/*  BOUTONS */
.actions {
    display:flex;
    gap:10px;
    margin-top:15px;
}

button {
    padding:10px 18px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
}

.btn-search {
    background:#3498db;
    color:white;
}

.btn-reset {
    background: #e74c3c;
    color: white;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: bold;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 14px;
    line-height: normal;
}

.btn-export {
    background:#2ecc71;
    color:white;
}

/* TABLE */
table {
    width:100%;
    border-collapse:collapse;
    margin-top: 20px;
}

th {
    background:#2c3e50;
    color:white;
    padding:10px;
}

td {
    padding:8px;
    border-bottom:1px solid #ddd;
}

tr:hover {
    background:#f4f6f8;
}

/* NAV */
.nav-buttons {
    display:flex;
    gap:10px;
    margin-bottom:20px;
}

.nav-buttons a {
    display:inline-block;
    padding:10px 16px;
    border-radius:8px;
    background:#2c3e50;
    color:white;
    text-decoration:none;
    font-weight:bold;
    transition:0.2s;
}

.nav-buttons a:hover {
    background:#3498db;
}

/* EXPORT */
.export-buttons {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.btn-export.csv { background: #27ae60; }
.btn-export.excel { background: #2ecc71; }

</style>

<div class="container">

<h2>Recherche avancée</h2>

<div class="nav-buttons">
    <a href="../index.php">🏠 Accueil</a>
    <a href="recherche.php">🔎 Recherche simple</a>
</div>

<div class="switch">
    <a href="?type=poste" class="<?= $type=='poste'?'active':'' ?>">Postes</a>
    <a href="?type=connexion" class="<?= $type=='connexion'?'active':'' ?>">Connexions</a>
</div>

<form method="GET" class="card">

<input type="hidden" name="type" value="<?= $type ?>">

<div class="filters">

<!-- SELECT SITE AVEC VERROUILLAGE AD -->
<select name="site" <?= !$_SESSION['is_admin'] ? 'disabled' : '' ?>>
<option value="">Tous les sites</option>
<?php
$sites = $pdo->query("SELECT * FROM sites")->fetchAll();
foreach($sites as $s){

    if (!$_SESSION['is_admin']) {
        $selected = ($_SESSION['site_id'] == $s['id']) ? 'selected' : '';
    } else {
        $selected = (($_GET['site'] ?? '') == $s['id']) ? 'selected' : '';
    }

    echo "<option value='{$s['id']}' $selected>{$s['nom']}</option>";
}
?>
</select>

<?php if (!$_SESSION['is_admin']): ?>
    <!-- Champ réel envoyé -->
    <input type="hidden" name="site" value="<?= $_SESSION['site_id'] ?>">
<?php endif; ?>

<?php if($type == "poste"): ?>

<input type="text" name="search" placeholder="Nom poste ou MAC">

<?php elseif($type == "connexion"): ?>

<input type="text" name="user" placeholder="Utilisateur">

<?php endif; ?>

</div>

<?php if($type == "poste"): ?>
<!-- Cases à cocher recherche avancée -->
<h3>Informations générales</h3>
<div class="checkbox-group">
<label><input type="checkbox" name="cols[]" value="adresse_mac"> MAC</label>
<label><input type="checkbox" name="cols[]" value="type_disque"> Disque</label>
<label><input type="checkbox" name="cols[]" value="ram_gio"> RAM</label>
<label><input type="checkbox" name="cols[]" value="cpu_model"> CPU</label>
<label><input type="checkbox" name="cols[]" value="cpu_freq_ghz"> Fréquence CPU</label>
<label><input type="checkbox" name="cols[]" value="os_version"> OS</label>
<label><input type="checkbox" name="cols[]" value="os_build"> Build</label>
<label><input type="checkbox" name="cols[]" value="os_arch"> Architecture</label>
</div>

<h3>Logiciels</h3>
<div class="checkbox-group">
<label><input type="checkbox" name="cols[]" value="version_chrome"> Chrome</label>
<label><input type="checkbox" name="cols[]" value="version_firefox"> Firefox</label>
<label><input type="checkbox" name="cols[]" value="version_internet_explorer"> IE</label>
<label><input type="checkbox" name="cols[]" value="version_dotnet"> DotNet</label>
<label><input type="checkbox" name="cols[]" value="version_client_citrix"> Citrix</label>
<label><input type="checkbox" name="cols[]" value="version_edictee"> eDictee</label>
<label><input type="checkbox" name="cols[]" value="version_cws"> CWS</label>
<label><input type="checkbox" name="cols[]" value="version_philips_speech_drivers"> Philips</label>
<label><input type="checkbox" name="cols[]" value="version_dragon"> Dragon</label>
<label><input type="checkbox" name="cols[]" value="version_office"> Office</label>
<label><input type="checkbox" name="cols[]" value="version_trend_micro"> Trend Micro</label>
<label><input type="checkbox" name="cols[]" value="version_cryptolib"> Cryptolib</label>
</div>

<?php endif; ?>

<?php if($type == "connexion"): ?>

<h3>Connexions</h3>
<div class="checkbox-group">
<label><input type="checkbox" name="cols_conn[]" value="nom_utilisateur"> Utilisateur</label>
<label><input type="checkbox" name="cols_conn[]" value="adresse_ip"> IP</label>
<label><input type="checkbox" name="cols_conn[]" value="serveur_auth"> Serveur</label>
<label><input type="checkbox" name="cols_conn[]" value="date_connexion"> Connexion</label>
<label><input type="checkbox" name="cols_conn[]" value="date_deconnexion"> Déconnexion</label>
<label><input type="checkbox" name="cols_conn[]" value="uptime"> Uptime</label>
<label><input type="checkbox" name="cols_conn[]" value="imprimante_defaut"> Imprimante</label>
<label><input type="checkbox" name="cols_conn[]" value="liste_imprimantes"> Liste imprimantes</label>
<label><input type="checkbox" name="cols_conn[]" value="lecteurs_reseaux"> Réseaux</label>
</div>

<?php endif; ?>

<div class="actions">
<button class="btn-search" type="submit" name="search_btn">Rechercher</button>
<a href="recherche_avancee.php" class="btn-reset">
    Réinitialiser
</a>
</div>

</form>

<?php
if(isset($_GET['search_btn'])){

$site = $_GET['site'] ?? '';

/* ============================================================
   MODE POSTE — AVEC FILTRE AD
============================================================ */
if($type == "poste"){

$cols = $_GET['cols'] ?? [];
$select = "DISTINCT p.nom_poste";

foreach($cols as $c){
    $select .= ", p.$c";
}

$sql = "SELECT $select FROM postes p WHERE 1=1 $filter_poste";
$params = [];

if($site){
    $sql .= " AND p.id_site = :site";
    $params['site'] = $site;
}

if(!empty($_GET['search'])){
    $sql .= " AND (p.nom_poste LIKE :search OR p.adresse_mac LIKE :search)";
    $params['search'] = "%".$_GET['search']."%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo "<form method='POST' action='export_avance.php'>";
echo "<input type='hidden' name='query' value='".htmlspecialchars($sql, ENT_QUOTES)."'>";
foreach($params as $k=>$v){
    echo "<input type='hidden' name='$k' value='$v'>";
}
echo "<input type='hidden' name='type' value='poste'>";

echo "<div class='export-buttons'>";
echo "<button class='btn-export csv' name='format' value='csv'>📄 Export CSV</button>";
echo "<button class='btn-export excel' name='format' value='excel'>📊 Export Excel</button>";
echo "</div>";

echo "</form>";

echo "<table><tr><th>Poste</th>";

foreach($cols as $c){
    echo "<th>$c</th>";
}

echo "</tr>";

while($row = $stmt->fetch()){
    echo "<tr><td>".$row['nom_poste']."</td>";
    foreach($cols as $c){
        echo "<td>".$row[$c]."</td>";
    }
    echo "</tr>";
}
echo "</table>";

}

/* ============================================================
   MODE CONNEXION — AVEC FILTRE AD
============================================================ */
if($type == "connexion"){

$cols = $_GET['cols_conn'] ?? [];
$select = "p.nom_poste";

foreach($cols as $c){
    $select .= ", c.$c";
}

$sql = "
SELECT $select
FROM connexions c
JOIN postes p ON c.id_poste = p.id
WHERE 1=1
$filter_conn
";

$params = [];

if($site){
    $sql .= " AND p.id_site = :site";
    $params['site'] = $site;
}

if(!empty($_GET['user'])){
    $sql .= " AND c.nom_utilisateur LIKE :user";
    $params['user'] = "%".$_GET['user']."%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo "<form method='POST' action='export_avance.php'>";
echo "<input type='hidden' name='query' value='".htmlspecialchars($sql, ENT_QUOTES)."'>";
foreach($params as $k=>$v){
    echo "<input type='hidden' name='$k' value='$v'>";
}
echo "<input type='hidden' name='type' value='connexion'>";
echo "<button class='btn-export' name='format' value='csv'>CSV</button>";
echo "<button class='btn-export' name='format' value='excel'>Excel</button>";
echo "</form>";

echo "<table><tr><th>Poste</th>";

foreach($cols as $c){
    echo "<th>$c</th>";
}

echo "</tr>";

while($row = $stmt->fetch()){
    echo "<tr><td>".$row['nom_poste']."</td>";
    foreach($cols as $c){
        echo "<td>".$row[$c]."</td>";
    }
    echo "</tr>";
}
echo "</table>";

}

}
?>

</div>

<?php include "../includes/footer.php"; ?>

