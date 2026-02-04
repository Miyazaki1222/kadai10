<?php
require_once('../db_config.php');

$id = $_POST['id'];
$team_id = $_POST['team_id'];
$player_name = $_POST['player_name'];
$back_number = $_POST['back_number'];
$member_type = $_POST['member_type'];

if (!empty($id)) {
    $stmt = $pdo->prepare("UPDATE members SET team_id=:team_id, player_name=:player_name, back_number=:back_number, member_type=:member_type WHERE id=:id");
    $stmt->bindValue(':team_id', $team_id, PDO::PARAM_INT);
    $stmt->bindValue(':player_name', $player_name, PDO::PARAM_STR);
    $stmt->bindValue(':back_number', $back_number, PDO::PARAM_INT);
    $stmt->bindValue(':member_type', $member_type, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status == false) {
        exit("UpdateError");
    } else {
        header("Location: ../team/team_admin.php");
        exit;
    }
}
?>