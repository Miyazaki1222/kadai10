<?php
require_once('../db_config.php');

// 試合IDごとに最新の保存日時と、登録人数をカウント
$sql = "SELECT game_id, MAX(created_at) as saved_at, COUNT(*) as p_count 
        FROM game_orders 
        GROUP BY game_id 
        ORDER BY saved_at DESC";
$stmt = $pdo->query($sql);
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>過去のオーダー一覧</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>過去のオーダー履歴</h4>
            <a href="order_kanban.php" class="btn btn-secondary">新規作成に戻る</a>
        </div>
        
        <div class="card shadow">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>試合ID (Game ID)</th>
                            <th>登録選手数</th>
                            <th>保存日時</th>
                            <th class="text-end">アクション</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $row): ?>
                        <tr>
                            <td class="fw-bold"><?=htmlspecialchars($row['game_id'])?></td>
                            <td><?=$row['p_count']?> 名</td>
                            <td class="text-muted"><?=$row['saved_at']?></td>
                            <td class="text-end">
                                <a href="order_kanban.php?copy_from=<?=urlencode($row['game_id'])?>" class="btn btn-sm btn-success">
                                    このオーダーをコピーして編集
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($list)): ?>
                            <tr><td colspan="4" class="text-center py-4">履歴はありません。</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>