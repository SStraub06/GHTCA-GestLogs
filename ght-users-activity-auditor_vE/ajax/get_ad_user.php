<?php
header("Content-Type: application/json");

/*
---------------------------------------------------------
CONFIG LDAP (AD Pasteur)
---------------------------------------------------------
*/

$ldap_server  = "ldap://ldap.hcc-pasteur.fr";
$ldap_domain  = "hcc-pasteur.fr";
$ldap_base_dn = "DC=hcc-pasteur,DC=fr";

// Compte de service AD
//$ldap_bind_user = "infostage3";   
//$ldap_bind_pass = "tatooine2006!";               


/*
---------------------------------------------------------
CONNEXION LDAP
---------------------------------------------------------
*/

$ldap = ldap_connect($ldap_server);

if (!$ldap) {
    echo json_encode(["error" => "Impossible de contacter le serveur AD"]);
    exit;
}

ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

$bind = @ldap_bind($ldap, $ldap_bind_user, $ldap_bind_pass);

if (!$bind) {
    echo json_encode(["error" => "Échec d'authentification AD"]);
    exit;
}


/*
---------------------------------------------------------
🔍 RECHERCHE UTILISATEUR PAR sAMAccountName
---------------------------------------------------------
*/

$user = $_GET['u'] ?? '';

if (!$user) {
    echo json_encode(["error" => "Aucun utilisateur fourni"]);
    exit;
}

// IMPORTANT : commence par (gère 5091119 vs 5091119-a)
$filter = "(sAMAccountName=$user*)";

$attributes = ["sn", "givenName", "sAMAccountName"];

$search = @ldap_search($ldap, $ldap_base_dn, $filter, $attributes);

if (!$search) {
    echo json_encode(["error" => "Erreur lors de la recherche AD"]);
    exit;
}

$entries = ldap_get_entries($ldap, $search);

if ($entries["count"] > 0) {
    echo json_encode([
        "sn"             => $entries[0]["sn"][0] ?? "",
        "givenName"      => $entries[0]["givenname"][0] ?? "",
        "sAMAccountName" => $entries[0]["samaccountname"][0] ?? ""
    ]);
} else {
    echo json_encode(["error" => "Utilisateur introuvable"]);
}

ldap_unbind($ldap);
