<?php
require_once('../db_config.php');
$id = $_GET['id'];

if (!empty($id)) {
    $stmt = $pdo->prepare("DELETE FROM members WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();

    if ($status == false) {
        exit("DeleteError");
    } else {
        header("Location: ../team/team_admin.php");
        exit;
    }
}
?>