<?php
session_start();

include "../config/database.php";
include "../includes/security.php";
include "../includes/header.php";

// filtre automatique (IMPORTANT)
$filter_poste = str_replace("WHERE", "AND", getSiteFilter("p"));
$filter_conn  = str_replace("WHERE", "AND", getConnexionFilter("c", "p"));

function displayValue($value) {
    if (empty($value) || strtolower($value) === 'absent') {
        return "<span class='missing'>Absent</span>";
    }
    return "<span class='ok'>" . htmlspecialchars($value) . "</span>";
}

$id = $_GET['id'] ?? 0;

// Récupération du poste (filtré AD)
$stmt = $pdo->prepare("
SELECT p.*, s.nom as site_nom
FROM postes p
JOIN sites s ON p.id_site = s.id
WHERE p.id = :id
$filter_poste
");
$stmt->execute(['id' => $id]);
$poste = $stmt->fetch();

if (!$poste) {
    die("Poste introuvable");
}

// Récupération des connexions (filtré AD)
$stmt = $pdo->prepare("
SELECT c.*
FROM connexions c
JOIN postes p ON c.id_poste = p.id
WHERE c.id_poste = :id
$filter_conn
ORDER BY c.date_connexion DESC
");
$stmt->execute(['id' => $id]);
$connexions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Détail poste</title>
    <style>
        body { font-family: Arial; background:#f5f6fa; }
        .container { width:90%; margin:auto; margin-top:30px; }
        .card { background:white; padding:20px; margin-bottom:20px; border-radius:10px; }
        h2 { margin-top:0; }

        .grid {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }

        .col {
            flex: 1;
            min-width: 300px;
        }

        .table-container { overflow-x: auto; }

        table {
            width:100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th, td {
            padding:10px;
            border:1px solid #ddd;
            word-wrap: break-word;
        }

        th { background:#2f3640; color:white; }
    </style>
</head>

<body>

<div class="container">

    <h2>Détail du poste : <?= $poste['nom_poste'] ?></h2>

    <div class="back-button">
        <a href="recherche.php?search=<?= $_GET['search'] ?? '' ?>&site=<?= $_GET['site'] ?? '' ?>">
            ⬅ Retour à la recherche
        </a>
    </div>

    <div class="card">
        <div class="grid">
<!-- Données BDD Tableau détail du poste, lien cliquable dans la barre de recherche -->
            <div class="col">
                <h3>Informations générales</h3>
                <p><b>Site :</b> <?= $poste['site_nom'] ?></p>
                <p><b>Adresse MAC :</b> <?= $poste['adresse_mac'] ?></p>
                <p><b>Type du disque :</b> <?= $poste['type_disque'] ?></p>
                <p><b>OS Build :</b> <?= $poste['os_build'] ?></p>
                <p><b>OS Architecture :</b> <?= $poste['os_arch'] ?> bits</p>
                <p><b>RAM :</b> <?= $poste['ram_gio'] ?> Go</p>
                <p><b>CPU :</b> <?= $poste['cpu_model'] ?></p>
                <p><b>Fréquence CPU :</b> <?= $poste['cpu_freq_ghz'] ?> GHz</p>
                <p><b>OS :</b> <?= $poste['os_version'] ?> (<?= $poste['os_arch'] ?>)</p>
            </div>

            <div class="col">
                <h3>Versions des logiciels</h3>

                <p><b>Firefox :</b> <?= displayValue($poste['version_firefox']) ?></p>
                <p><b>Chrome :</b> <?= displayValue($poste['version_chrome']) ?></p>
                <p><b>Internet Explorer :</b> <?= displayValue($poste['version_internet_explorer']) ?></p>
                <p><b>DotNet :</b> <?= displayValue($poste['version_dotnet']) ?></p>
                <p><b>Client Citrix :</b> <?= displayValue($poste['version_client_citrix']) ?></p>
                <p><b>eDictee :</b> <?= displayValue($poste['version_edictee']) ?></p>
                <p><b>CWS :</b> <?= displayValue($poste['version_cws']) ?></p>
                <p><b>Philips Speech :</b> <?= displayValue($poste['version_philips_speech_drivers']) ?></p>
                <p><b>Dragon :</b> <?= displayValue($poste['version_dragon']) ?></p>
                <p><b>Office :</b> <?= displayValue($poste['version_office']) ?></p>
                <p><b>Trend Micro :</b> <?= displayValue($poste['version_trend_micro']) ?></p>
                <p><b>Cryptolib :</b> <?= displayValue($poste['version_cryptolib']) ?></p>
            </div>

        </div>
    </div>

    <div class="card">
        <h3>Historique des connexions</h3>

        <div class="table-container">
            <table>
                <tr>
                    <th>Utilisateur</th>
                    <th>IP</th>
                    <th>Serveur d'authentification</th>
                    <th>Date connexion</th>
                    <th>Date déconnexion</th>
                    <th>Uptime</th>
                    <th>Imprimante par défaut</th>
                    <th>Liste des imprimantes</th>
                    <th>Lecteurs réseaux</th>
                    <th>Temps d'exécution du script</th>
                </tr>

                <?php foreach ($connexions as $c): ?>
                <tr>
                    <td>
                        <span class="ad-user" data-user="<?= htmlspecialchars($c['nom_utilisateur']) ?>">
                            <?= htmlspecialchars($c['nom_utilisateur']) ?>
                        </span>
                    </td>
                    <td><?= $c['adresse_ip'] ?></td>
                    <td><?= $c['serveur_auth'] ?></td>
                    <td><?= $c['date_connexion'] ?></td>
                    <td><?= $c['date_deconnexion'] ?></td>
                    <td><?= $c['uptime'] ?></td>
                    <td><?= $c['imprimante_defaut'] ?></td>
                    <td><?= $c['liste_imprimantes'] ?></td>
                    <td><?= $c['lecteurs_reseaux'] ?></td>
                    <td><?= $c['temps_execution_script'] ?></td>
                </tr>
                <?php endforeach; ?>

            </table>
        </div>
    </div>

</div>

<style>
.back-button a {
    display: inline-block;
    background: #2f3640;
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
}
.back-button a:hover { background:#0984e3; }

.ok {
    color: #2e7d32;
    background: #e8f5e9;
    padding: 3px 8px;
    border-radius: 6px;
}

.missing {
    color: #c62828;
    background: #fdecea;
    padding: 3px 8px;
    border-radius: 6px;
}
</style>

</body>
</html>

<?php include "../includes/footer.php"; ?>
