<?php
require_once __DIR__ . "/auth_check.php";
require_once __DIR__ . "/security.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>GHTCA Users Activity Auditor</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f5f6fa;
        }

        /* 🔥 Bouton déconnexion sous la navbar */
        .logout-container {
            position: fixed;
            top: 70px; /* sous la barre */
            right: 20px;
            z-index: 9999;
        }

        .logout-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: 0.25s ease;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #ff6b5a, #d64541);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.25);
        }

        /* 🔹 NAVBAR */
        .navbar {
            background: #2f3640;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1000;
        }

        .logo {
            color: white;
            font-size: 18px;
            font-weight: bold;
        }

        .menu {
            display: flex;
            gap: 20px;
        }

        .menu a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .menu a:hover {
            background: #0984e3;
        }

        .menu a.active {
            background: #0984e3;
        }

        /* Tooltip AD */
        .tooltip {
            position: absolute;
            background: #2f3640;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            white-space: nowrap;
            z-index: 99999;
            display: none;
        }
    </style>
</head>

<body>

<!-- 🔥 Bouton déconnexion -->
<div class="logout-container">
    <a href="/ght-users-activity-auditor_v2/logout.php" class="logout-btn">Déconnexion</a>
</div>

<!-- 🔹 NAVBAR -->
<div class="navbar">
    
    <div class="logo">
        GHTCA GestLogs
    </div>

    <div class="menu">
        <a href="/ght-users-activity-auditor_v2/pages/dashboard.php">Dashboard</a>
        <a href="/ght-users-activity-auditor_v2/pages/recherche.php">Recherche</a>
        <a href="/ght-users-activity-auditor_v2/pages/poste.php">Postes</a>
        <a href="/ght-users-activity-auditor_v2/pages/logiciels.php">Logiciels</a>
        <a href="/ght-users-activity-auditor_v2/pages/connexions.php">Connexions</a>
        <a href="/ght-users-activity-auditor_v2/pages/statistiques.php">Statistiques</a>
    </div>

</div>

<!-- Tooltip AD global -->
<div id="ad-tooltip" class="tooltip"></div>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const tooltip = document.getElementById("ad-tooltip");

    // Délégation d'événements pour gérer aussi les .ad-user ajoutés après
    document.addEventListener("mouseenter", async function(e) {
        const target = e.target;
        if (!target.classList.contains("ad-user")) return;

        const user = target.dataset.user;

        // Matricule type 5091119 → 6 à 8 chiffres
        if (!/^\d{6,8}$/.test(user)) return;

        try {
            const res = await fetch("/ght-users-activity-auditor/ajax/get_ad_user.php?u=" + encodeURIComponent(user));
            const data = await res.json();

            if (data.error) {
                tooltip.innerHTML = "Utilisateur introuvable";
            } else {
                tooltip.innerHTML = `
                    <b>${data.givenName} ${data.sn}</b><br>
                    Matricule : ${data.sAMAccountName}
                `;
            }

            tooltip.style.display = "block";
            tooltip.style.left = (e.pageX + 15) + "px";
            tooltip.style.top = (e.pageY + 15) + "px";

        } catch (err) {
            tooltip.innerHTML = "Erreur AD";
            tooltip.style.display = "block";
            tooltip.style.left = (e.pageX + 15) + "px";
            tooltip.style.top = (e.pageY + 15) + "px";
        }

    }, true);

    document.addEventListener("mousemove", function(e) {
        if (tooltip.style.display === "block") {
            tooltip.style.left = (e.pageX + 15) + "px";
            tooltip.style.top = (e.pageY + 15) + "px";
        }
    });

    document.addEventListener("mouseleave", function(e) {
        if (e.target.classList && e.target.classList.contains("ad-user")) {
            tooltip.style.display = "none";
        }
    }, true);
});
</script>
