<?php
session_start();

include "../config/database.php";
include "../includes/security.php";
include "../includes/header.php";

// Si pas admin → site forcé
if (!$_SESSION['is_admin']) {
    $_GET['site'] = $_SESSION['site_id'];
}

$filter_poste = getSiteFilter("p");
$filter_conn = getConnexionFilter("c", "p");
?>

<style>
body {
    font-family: Arial;
    background: #f5f6fa;
}

.title {
    text-align: center;
    margin-top: 20px;
    color: #2f3640;
}

.search-container {
    width: 80%;
    margin: 20px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.search-bar {
    display: flex;
    gap: 10px;
}

.search-bar input {
    flex: 1;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

.search-bar button {
    background: #0984e3;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
}

.search-bar button:hover {
    background: #74b9ff;
}

.filters {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}

.filters select,
.filters input {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

.results {
    width: 80%;
    margin: 20px auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
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
    font-weight: bold;
}

a:hover {
    text-decoration: underline;
}

.btn-advanced {
    padding: 10px 15px;
    background: #2f3640;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    margin-left: 10px;
    transition: 0.2s;
    font-size: 14px;
}

.btn-advanced:hover {
    background: #0984e3;
}
</style>

<h1 class="title">Recherche des logs</h1>

<div class="search-container">

    <div class="search-bar">
        <input type="text" id="search" 
value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
placeholder="Nom poste, utilisateur, IP, MAC...">
        <button onclick="searchLogs()">Rechercher</button>
        <a href="recherche_avancee.php" class="btn-advanced">
            Recherche avancée
        </a>
    </div>

    <div class="filters">
<!-- Filtrage GHTCA -->
        <select id="site" <?= !$_SESSION['is_admin'] ? 'disabled' : '' ?>>
            <option value="">Tous les sites</option>

            <?php
            $sites = $pdo->query("SELECT * FROM sites");
            $currentSite = $_GET['site'] ?? '';

            while($s = $sites->fetch()){
                if (!$_SESSION['is_admin']) {
                    $selected = ($_SESSION['site_id'] == $s['id']) ? 'selected' : '';
                } else {
                    $selected = ($currentSite == $s['id']) ? 'selected' : '';
                }
                echo "<option value='".$s['id']."' $selected>".$s['nom']."</option>";
            }
            ?>

        </select>

        <input type="date" id="date_debut">
        <input type="date" id="date_fin">

    </div>

</div>

<div class="results">

<table>
<!-- Colonne tableau résultat de la recherche -->
<thead>
<tr>
<th>Poste</th>
<th>Utilisateur</th>
<th>Adresse IP</th>
<th>Adresse MAC</th>
<th>Date connexion</th>
</tr>
</thead>

<tbody id="results">
<tr>
<td colspan="5" style="text-align:center;">Aucune recherche effectuée</td>
</tr>
</tbody>

</table>

</div>

<script>
window.onload = function() {
    const search = "<?= $_GET['search'] ?? '' ?>";
    const site = "<?= $_GET['site'] ?? '' ?>";

    if (search !== "" || site !== "") {
        searchLogs();
    }
};
</script>

<script src="../assets/js/search.js"></script>

<?php include "../includes/footer.php"; ?>
