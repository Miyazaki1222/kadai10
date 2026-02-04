<?php
require_once('../db_config.php');

// JSON形式のポストデータを受け取る
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$game_id = $data['game_id'];
$orders = $data['orders'];

if (!$game_id || empty($orders)) {
    echo json_encode(['status' => 'error', 'message' => 'データが不足しています']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 既存の同じ試合IDのオーダーを一旦削除（上書き更新のため）
    $stmt = $pdo->prepare("DELETE FROM game_orders WHERE game_id = :game_id");
    $stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
    $stmt->execute();

    // 新しいオーダーを挿入
    $stmt = $pdo->prepare("INSERT INTO game_orders (game_id, player_id, position_num, batting_order) VALUES (:game_id, :player_id, :pos, :bat)");

    foreach ($orders as $row) {
        $stmt->bindValue(':game_id', $game_id, PDO::PARAM_STR);
        $stmt->bindValue(':player_id', $row['player_id'], PDO::PARAM_INT);
        $stmt->bindValue(':pos', $row['position'], PDO::PARAM_STR);
        $stmt->bindValue(':bat', $row['batting_order'], PDO::PARAM_INT);
        $stmt->execute();
    }

    $pdo->commit();
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>