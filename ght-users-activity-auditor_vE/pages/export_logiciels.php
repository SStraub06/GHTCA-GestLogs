<?php
session_start();

require_once "../config/database.php";
require_once "../includes/security.php";

// 🔒 filtre automatique (IMPORTANT)
$filter_poste = str_replace("WHERE", "AND", getSiteFilter("p"));
$filter_conn  = str_replace("WHERE", "AND", getConnexionFilter("c", "p"));

$site = $_GET['site'] ?? '';
$logiciel = $_GET['logiciel'] ?? '';
$format = $_GET['format'] ?? 'csv';

// 🔒 Sécurité
$allowed = [
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

if (!in_array($logiciel, $allowed)) {
    die("Erreur");
}

// 🔹 Requête AVEC filtre AD
$stmt = $pdo->prepare("
SELECT 
    p.nom_poste, 
    p.$logiciel AS version,
    CASE 
        WHEN p.$logiciel = 'Absent' THEN 'SANS'
        ELSE 'AVEC'
    END AS statut
FROM postes p
WHERE p.id_site = :site
$filter_poste
ORDER BY 
    (p.$logiciel = 'Absent') ASC,
    p.nom_poste ASC
");

$stmt->execute(['site' => $site]);

// ================= CSV =================
if ($format == "csv") {

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=export_logiciels.csv');

    $output = fopen("php://output", "w");

    fputcsv($output, ['Statut', 'Poste', 'Version'], ';');

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['statut'],
            $row['nom_poste'],
            $row['version']
        ], ';');
    }

    fclose($output);
    exit;
}

// ================= EXCEL =================
if ($format == "excel") {

    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=export_logiciels.xls");

    echo "Statut\tPoste\tVersion\n";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        echo $row['statut'] . "\t" .
             $row['nom_poste'] . "\t" .
             $row['version'] . "\n";
    }

    exit;
}

