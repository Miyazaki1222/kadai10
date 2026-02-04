<?php
require_once('../db_config.php');

// 1. 全メンバー取得
$stmt = $pdo->prepare("SELECT * FROM members");
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. コピー流用データの取得（URLに copy_from がある場合）
$copy_data = [];
$display_game_id = "";
if (isset($_GET['copy_from'])) {
    $stmt = $pdo->prepare("SELECT * FROM game_orders WHERE game_id = :gid");
    $stmt->bindValue(':gid', $_GET['copy_from'], PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($rows as $r) {
        // player_id をキーにして配置情報を格納
        $copy_data[$r['player_id']] = [
            'pos' => $r['position_num'],
            'bat' => $r['batting_order']
        ];
    }
    // コピー元を識別しやすくするため、入力欄に初期値を入れる（任意で変更可能）
    $display_game_id = $_GET['copy_from'] . "_copy";
}

$pos_options = ['投', '捕', '一', '二', '三', '遊', '左', '中', '右', '指'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>野球オーダー作成</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg-light">
    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded shadow-sm">
            <h5 class="mb-0">⚾ オーダー編成ボード</h5>
            <div class="d-flex gap-2">
                <a href="order_history.php" class="btn btn-outline-secondary btn-sm">過去のオーダー一覧</a>
                <input type="text" id="game_id" class="form-control form-control-sm" placeholder="試合ID" style="width:150px;" value="<?=$display_game_id?>">
                <button id="saveBtn" class="btn btn-primary btn-sm">保存確定</button>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white py-2">選手名簿</div>
                    <div class="player-list bench-area" id="bench">
                        <?php foreach($members as $m): ?>
                            <?php if (!isset($copy_data[$m['id']])): // すでにオーダーに入っている選手は出さない ?>
                                <div class="player-item" data-id="<?=$m['id']?>">
                                    <span class="badge bg-dark">#<?=$m['back_number']?></span>
                                    <span class="ms-1"><?=$m['player_name']?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white py-2 text-center">スターティングラインナップ</div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead class="table-light text-center small">
                                <tr>
                                    <th style="width: 15%;">打順</th>
                                    <th style="width: 55%;">選手名</th>
                                    <th style="width: 30%;">守備</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for($i=1; $i<=9; $i++): ?>
                                <tr>
                                    <td class="text-center fw-bold bg-light"><?=$i?></td>
                                    <td class="p-1">
                                        <div class="player-list order-slot" id="order-<?=$i?>" data-order="<?=$i?>">
                                            <?php 
                                            // コピーデータからこの打順の選手を探す
                                            foreach($members as $m) {
                                                if (isset($copy_data[$m['id']]) && $copy_data[$m['id']]['bat'] == $i) {
                                                    echo '<div class="player-item" data-id="'.$m['id'].'">
                                                            <span class="badge bg-dark">#'.$m['back_number'].'</span>
                                                            <span class="ms-1">'.$m['player_name'].'</span>
                                                          </div>';
                                                    $selected_pos = $copy_data[$m['id']]['pos'];
                                                }
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm pos-select">
                                            <?php foreach($pos_options as $p): ?>
                                                <option value="<?=$p?>" <?=(isset($selected_pos) && $selected_pos == $p)?'selected':''?>><?=$p?></option>
                                            <?php endforeach; unset($selected_pos); // ループごとにクリア ?>
                                        </select>
                                    </td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm bg-dark text-white p-3 text-center" style="height: 100%;">
                    <h6>守備配置プレビュー</h6>
                    <div class="field-preview mt-3">
                        <p class="small text-muted">中央で設定した内容が反映されます</p>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script src="../script.js"></script>
</body>
</html>