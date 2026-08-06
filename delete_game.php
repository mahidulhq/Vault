<?php
// Technologies Used: PHP & MySQL (Delete Operation)
session_start();
require 'db.php';

if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM Games WHERE Game_ID = ?");
    $stmt->execute([$id]);
}

header("Location: admin_dashboard.php");
exit;
?>