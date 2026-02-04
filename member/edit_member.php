<?php
require_once('../db_config.php');

// 役割の定義（管理画面と合わせる）
$member_type = [
    1 => '選手',
    2 => 'コーチ',
    3 => '監督',
    4 => '元チームメンバー'
];

$id = $_GET['id'];

// 該当選手のデータ取得
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$member = $stmt->fetch(PDO::FETCH_ASSOC);

// チーム一覧
$teams = $pdo->query("SELECT * FROM teams")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メンバー編集</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container">
        <div class="card shadow mx-auto" style="max-width: 500px;">
            <div class="card-header bg-primary text-white">メンバー情報の編集</div>
            <form action="update_member.php" method="POST" class="card-body">
                <input type="hidden" name="id" value="<?=$member['id']?>">
                
                <label class="small text-muted">所属チーム</label>
                <select name="team_id" class="form-select mb-3">
                    <?php foreach($teams as $t): ?>
                        <option value="<?=$t['id']?>" <?=$t['id']==$member['team_id']?'selected':''?>>
                            <?=$t['team_name']?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="small text-muted">役割</label>
                <select name="member_type" class="form-select mb-3" required>
                    <?php foreach($member_type as $key => $val): ?>
                        <option value="<?=$key?>" <?=$key == $member['member_type'] ? 'selected' : ''?>>
                            <?=$val?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="small text-muted">氏名</label>
                <input type="text" name="player_name" class="form-control mb-3" value="<?=$member['player_name']?>" required>

                <label class="small text-muted">背番号</label>
                <input type="number" name="back_number" class="form-control mb-4" value="<?=$member['back_number']?>" min="0" required>

                <div class="d-flex justify-content-between">
                    <a href="../team/team_admin.php" class="btn btn-secondary">戻る</a>
                    <button type="submit" class="btn btn-success">更新する</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>