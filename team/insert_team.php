<?php
require_once('../db_config.php');

$team_name = $_POST['team_name'];

if (!empty($team_name)) {
    $stmt = $pdo->prepare("INSERT INTO teams (team_name) VALUES (:team_name)");
    $stmt->bindValue(':team_name', $team_name, PDO::PARAM_STR);
    $status = $stmt->execute();

    if ($status == false) {
        exit("ErrorQuery");
    } else {
        header("Location: team_admin.php");
        exit;
    }
}