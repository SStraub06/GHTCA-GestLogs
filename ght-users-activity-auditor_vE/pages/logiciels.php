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

/* ---------------------------------------------------------
    filtre automatique (IMPORTANT)
--------------------------------------------------------- */
$filter_poste = str_replace("WHERE", "AND", getSiteFilter("p"));
$filter_conn  = str_replace("WHERE", "AND", getConnexionFilter("c", "p"));


// Récupération des filtres
$site = $_GET['site'] ?? '';
$logiciel = $_GET['logiciel'] ?? 'version_chrome';

//  Sécurité (très important)
$logiciels_autorises = [
    'version_chrome',
    'version_firefox',
    'version_internet_explorer',
    'version_dotnet',
    'version_client_citrix',
    'version_edictee',
    'version_cws',
    'version_philips_speech_drivers',
    'version_dragon',
    'version_office',
    'version_trend_micro',
    'version_cryptolib'
];

if (!in_array($logiciel, $logiciels_autorises)) {
    $logiciel = 'version_chrome';
}

// Labels propres
$labels = [
    'version_chrome' => 'Chrome',
    'version_firefox' => 'Firefox',
    'version_internet_explorer' => 'Internet Explorer',
    'version_dotnet' => 'DotNet',
    'version_client_citrix' => 'Client Citrix',
    'version_edictee' => 'eDictee',
    'version_cws' => 'CWS',
    'version_philips_speech_drivers' => 'Philips Speech',
    'version_dragon' => 'Dragon',
    'version_office' => 'Office',
    'version_trend_micro' => 'Trend Micro',
    'version_cryptolib' => 'Cryptolib'
];

$nom_logiciel = $labels[$logiciel];
?>

<h1 class="title">Audit des logiciels</h1>

<div class="search-container">

    <h3 class="filter-title">Analyse des logiciels par site</h3>

    <form method="GET" class="filters" action="export_logiciels.php">

    <!-- 🔹 Site -->
    <select name="site" <?= !$_SESSION['is_admin'] ? 'disabled' : '' ?>>
        <option value="">Choisir un site</option>
        <?php
        $sites = $pdo->query("SELECT * FROM sites")->fetchAll();
        foreach ($sites as $s) {

            if (!$_SESSION['is_admin']) {
                $selected = ($_SESSION['site_id'] == $s['id']) ? 'selected' : '';
            } else {
                $selected = ($site == $s['id']) ? 'selected' : '';
            }

            echo "<option value='{$s['id']}' $selected>{$s['nom']}</option>";
        }
        ?>
    </select>

    <?php if (!$_SESSION['is_admin']): ?>
        <!-- 🔒 Champ réel envoyé au backend -->
        <input type="hidden" name="site" value="<?= $_SESSION['site_id'] ?>">
    <?php endif; ?>

    <!-- 🔹 Logiciel -->
    <select name="logiciel">
        <?php
        foreach ($labels as $key => $label) {
            $selected = ($logiciel == $key) ? 'selected' : '';
            echo "<option value='$key' $selected>$label</option>";
        }
        ?>
    </select>

    <!-- 🔹 Bouton analyser (reste sur la page) -->
    <button type="submit" formaction="">Analyser</button>

    <!-- 🔹 Export CSV -->
    <button type="submit" name="format" value="csv">
        Export CSV
    </button>

    <!-- 🔹 Export Excel -->
    <button type="submit" name="format" value="excel">
        Export Excel
    </button>

</form>

</div>

<style>
/* ----------------------------
   Style CSS
---------------------------- */

/* ----------------------------
   TITRE
---------------------------- */
.title {
    font-size: 26px;
    margin-bottom: 20px;
    color: #2c3e50;
}

/* ----------------------------
   SEARCH CONTAINER
---------------------------- */
.search-container {
    background: #ffffff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.filter-title {
    margin-bottom: 15px;
    font-size: 16px;
    color: #34495e;
}

/* FORM */
.filters {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

/* SELECT */
.filters select {
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    background: #f8f9fa;
    font-size: 14px;
    min-width: 180px;
    transition: 0.2s;
}

.filters select:focus {
    border-color: #3498db;
    background: #fff;
    outline: none;
}

/* BOUTON */
.filters button {
    padding: 10px 20px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.2s;
}

.filters button:hover {
    background: #2980b9;
    transform: translateY(-1px);
}

/* ----------------------------
   STATS CARDS
---------------------------- */
.stats-cards {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.card {
    flex: 1;
    padding: 18px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

/* AVEC */
.card.ok {
    background: #e8f8f0;
    color: #27ae60;
    border-left: 5px solid #2ecc71;
}

/* SANS */
.card.ko {
    background: #fdecea;
    color: #c0392b;
    border-left: 5px solid #e74c3c;
}

/* ----------------------------
   LISTES POSTES
---------------------------- */
.liste-postes {
    list-style: none;
    padding: 0;
    margin-bottom: 30px;
}

.liste-postes li {
    background: #ffffff;
    margin-bottom: 8px;
    padding: 10px 14px;
    border-radius: 8px;
    transition: 0.2s;
    border: 1px solid #eee;
}

.liste-postes li a {
    text-decoration: none;
    color: #2c3e50;
    display: block;
    font-size: 14px;
}

.liste-postes li:hover {
    background: #f4f6f8;
    transform: translateX(3px);
}

/* ----------------------------
   TITRES SECTIONS
---------------------------- */
h3 {
    margin: 20px 0 10px;
    color: #2c3e50;
}

/* ----------------------------
   RESPONSIVE
---------------------------- */
@media (max-width: 768px) {

    .filters {
        flex-direction: column;
        align-items: stretch;
    }

    .filters select,
    .filters button {
        width: 100%;
    }

    .stats-cards {
        flex-direction: column;
    }
}

</style>

<?php
if ($site) {

    // Compteurs
    $stmt = $pdo->prepare("
        SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN $logiciel != 'Absent' THEN 1 ELSE 0 END) as avec,
        SUM(CASE WHEN $logiciel = 'Absent' THEN 1 ELSE 0 END) as sans
        FROM postes p
        WHERE p.id_site = :site
        $filter_poste
    ");
    $stmt->execute(['site' => $site]);
    $res = $stmt->fetch();

    echo "<div class='stats-cards'>";
    echo "<div class='card ok'>✔ ".$res['avec']." postes avec $nom_logiciel</div>";
    echo "<div class='card ko'>❌ ".$res['sans']." postes sans $nom_logiciel</div>";
    echo "</div>";

    // AVEC logiciel
    $stmt1 = $pdo->prepare("
        SELECT p.id, p.nom_poste, p.$logiciel  
        FROM postes p
        WHERE p.$logiciel != 'Absent'
        AND p.id_site = :site
        $filter_poste
        ORDER BY p.nom_poste
    ");
    $stmt1->execute(['site' => $site]);

    echo "<h3>✔ Postes avec $nom_logiciel</h3>";
    echo "<ul class='liste-postes'>";

    while ($p = $stmt1->fetch()) {

    echo "<li style='display:flex; align-items:center; gap:10px;'>";

    echo "<a href='poste_detail.php?id=".$p['id']."'>
            ".$p['nom_poste']."
          </a>";

    echo "<span style='font-size:12px; color:#555;'>
            ".$p[$logiciel]."
          </span>";

    echo "</li>";
}

    echo "</ul>";

    // SANS logiciel
    $stmt2 = $pdo->prepare("
        SELECT p.id, p.nom_poste, p.$logiciel  
        FROM postes p
        WHERE p.$logiciel = 'Absent'
        AND p.id_site = :site
        $filter_poste
        ORDER BY p.nom_poste
    ");
    $stmt2->execute(['site' => $site]);

    echo "<h3>❌ Postes sans $nom_logiciel</h3>";
    echo "<ul class='liste-postes'>";

    while ($p = $stmt2->fetch()) {
        echo "<li>
            <a href='poste_detail.php?id=".$p['id']."'>
                ".$p['nom_poste']."
            </a>
        </li>";
    }

    echo "</ul>";
}
?>

<?php include "../includes/footer.php"; ?>

