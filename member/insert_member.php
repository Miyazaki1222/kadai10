<?php
require_once('../db_config.php');

$team_id = $_POST['team_id'];
$player_name = $_POST['player_name'];
$back_number = $_POST['back_number'];
$member_type = $_POST['member_type'];

var_dump($_POST);

if (!empty($player_name) && !empty($team_id)) {
    $stmt = $pdo->prepare("INSERT INTO members (team_id, player_name, back_number, member_type) VALUES (:team_id, :player_name, :back_number, :member_type)");
    $stmt->bindValue(':team_id', $team_id, PDO::PARAM_INT);
    $stmt->bindValue(':player_name', $player_name, PDO::PARAM_STR);
    $stmt->bindValue(':back_number', $back_number, PDO::PARAM_INT);
    $stmt->bindValue(':member_type', $member_type, PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status == false) {
        exit("ErrorQuery");
    } else {
        header("Location: ../team/team_admin.php");
        exit;
    }
}