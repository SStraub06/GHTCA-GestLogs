<?php
session_start();

include "../config/database.php";
include "../includes/security.php";

// filtre automatique (IMPORTANT)
$filter_poste = str_replace("WHERE", "AND", getSiteFilter("p"));
$filter_conn  = str_replace("WHERE", "AND", getConnexionFilter("c", "p"));

$query = $_POST['query'];
$format = $_POST['format'];

$params = $_POST;
unset($params['query'], $params['format'], $params['type']);

// Sécurisation : ajout du filtre AD dans la requête
// ----------------------------------------------------
// Si la requête contient "FROM postes p" → on applique $filter_poste
if (strpos($query, "FROM postes p") !== false) {
    $query = preg_replace("/WHERE 1=1/i", "WHERE 1=1 $filter_poste", $query);
}

// Si la requête contient "FROM connexions c" → on applique $filter_conn
if (strpos($query, "FROM connexions c") !== false) {
    $query = preg_replace("/WHERE 1=1/i", "WHERE 1=1 $filter_conn", $query);
}
// ----------------------------------------------------

$stmt = $pdo->prepare($query);
$stmt->execute($params);

/* CSV */
if($format == "csv"){
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=export.csv');

    $out = fopen("php://output","w");
    $first = true;

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if($first){
            fputcsv($out, array_keys($row));
            $first = false;
        }
        fputcsv($out, $row);
    }
    fclose($out);
    exit;
}

/* EXCEL */
if($format == "excel"){
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=export.xls");

    $first = true;

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if($first){
            echo implode("\t", array_keys($row))."\n";
            $first = false;
        }
        echo implode("\t", $row)."\n";
    }
    exit;
}

