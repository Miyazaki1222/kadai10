<?php
session_start();

// 1. ログインチェック（未ログインならログイン画面へ）
if (!isset($_SESSION['team_id'])) {
    header("Location: ../login/login.php");
    exit;
}

require_once('../db_config.php');

// セッションから情報を取得
$my_team_id = $_SESSION['team_id'];
$is_admin = ($_SESSION['role'] === 'admin'); // 管理者かどうか

// 権限設定のための定義
$member_type = [
    1 => '選手',
    2 => 'コーチ',
    3 => '監督',
    4 => '元チームメンバー'
];

// 2. チーム一覧取得（自分のチームのみ、または登録フォーム用）
$teams_stmt = $pdo->prepare("SELECT * FROM teams WHERE id = ?");
$teams_stmt->execute([$my_team_id]);
$teams = $teams_stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. メンバー一覧取得（自分のチームのメンバーのみに限定）
$sql = "SELECT m.*, t.team_name FROM members m 
        LEFT JOIN teams t ON m.team_id = t.id 
        WHERE m.team_id = ? 
        ORDER BY m.back_number ASC";
$members_stmt = $pdo->prepare($sql);
$members_stmt->execute([$my_team_id]);
$members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>チーム・メンバー管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .table-admin { font-size: 0.9rem; }
        .card-header { font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">チーム・メンバー管理 (ログイン中: <?= htmlspecialchars($teams[0]['team_name'] ?? '') ?>)</span>
        <div>
            <a href="../order/order_kanban.php" class="btn btn-outline-light btn-sm">オーダー作成へ</a>
            <a href="../logout/logout.php" class="btn btn-danger btn-sm ms-2">ログアウト</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row g-4">
        <div class="col-md-4">
            <?php if ($is_admin): ?>
                <div class="card shadow-sm mb-4 border-primary">
                    <div class="card-header bg-primary text-white">チーム管理（管理者のみ）</div>
                    <div class="card-body">
                        <p class="small text-muted">※チーム名の変更などはここで行います</p>
                        <form action="update_team.php" method="POST">
                            <input type="text" name="team_name" class="form-control mb-2" value="<?= htmlspecialchars($teams[0]['team_name'] ?? '') ?>" required>
                            <button type="submit" class="btn btn-primary btn-sm w-100">チーム名更新</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-success">
                    <div class="card-header bg-success text-white">メンバー新規登録</div>
                    <form action="../member/insert_member.php" method="POST" class="card-body">
                        <input type="hidden" name="team_id" value="<?= $my_team_id ?>">
                        <div class="mb-2">
                            <label class="small fw-bold">所属：<?= htmlspecialchars($teams[0]['team_name'] ?? '') ?></label>
                        </div>

                        <select name="member_type" class="form-select mb-2" required>
                            <option value="">役割を選択</option>
                            <?php foreach($member_type as $key => $val): ?>
                                <option value="<?=$key?>"><?=$val?></option>
                            <?php endforeach; ?>
                        </select>

                        <input type="text" name="player_name" class="form-control mb-2" placeholder="氏名" required>
                        <input type="number" name="back_number" class="form-control mb-2" placeholder="背番号" min="0" step="1" required>
                        
                        <button type="submit" class="btn btn-success btn-sm w-100">メンバー追加</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-secondary shadow-sm">
                    <i class="bi bi-info-circle"></i> 現在「閲覧モード」です。メンバーの追加・編集には管理者パスワードでのログインが必要です。
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span>登録名簿一覧</span>
                    <span class="badge bg-secondary"><?=count($members)?> 名登録済み</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 table-admin">
                        <thead class="table-light">
                            <tr>
                                <th>役割</th>
                                <th>背番号</th>
                                <th>氏名</th>
                                <?php if ($is_admin): ?>
                                    <th class="text-center">操作</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($members as $m): ?>
                            <tr>
                                <td><?= htmlspecialchars($member_type[$m['member_type']] ?? '不明') ?></td>
                                <td><?=$m['back_number']?></td>
                                <td><strong><?=htmlspecialchars($m['player_name'])?></strong></td>
                                
                                <?php if ($is_admin): ?>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="../member/edit_member.php?id=<?=$m['id']?>" class="btn btn-sm btn-outline-primary py-0">編集</a>
                                            <a href="../member/delete_member.php?id=<?=$m['id']?>" 
                                               class="btn btn-sm btn-outline-danger py-0"
                                               onclick="return confirm('本当に削除しますか？');">削除</a>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($members)): ?>
                                <tr><td colspan="<?= $is_admin ? 4 : 3 ?>" class="text-center text-muted p-4">メンバーが登録されていません</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>