<?php

function getUserSite(){
    return $_SESSION['site_id'] ?? null;
}

// 🔹 filtre pour table POSTES
function getSiteFilter($alias = "p"){

    $site = getUserSite();

    if(!$site){
        return "";
    }

    return "WHERE $alias.id_site = ".$site;
}

// 🔹 filtre pour CONNEXIONS
function getConnexionFilter($alias_conn = "c", $alias_poste = "p"){

    $site = getUserSite();

    if(!$site){
        return "";
    }

    return "WHERE $alias_poste.id_site = ".$site;
}