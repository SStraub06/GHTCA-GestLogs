<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /ght-users-activity-auditor_v2/login.php");
    exit;
}

header("Location: /ght-users-activity-auditor_v2/pages/dashboard.php");
exit;
