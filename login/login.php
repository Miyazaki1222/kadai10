<?php
session_start();
require_once('../db_config.php');

$error = "";

// すでにログインしている場合は管理画面へ
if (isset($_SESSION['team_id'])) {
    header("Location: ../team/team_admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_team_name = $_POST['team_name'] ?? '';
    $input_pass = $_POST['password'] ?? '';

    if ($input_team_name && $input_pass) {
        // 1. チーム名でDBを検索（idも一緒に取得するのがポイント）
        $stmt = $pdo->prepare("SELECT id, team_name, admin_password, user_password FROM teams WHERE team_name = ?");
        $stmt->execute([$input_team_name]);
        $team = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($team) {
            // 2. パスワードの判定（管理者か一般か）
            $is_admin = password_verify($input_pass, $team['admin_password']);
            $is_user  = password_verify($input_pass, $team['user_password']);

            if ($is_admin || $is_user) {
                // セッション固定攻撃対策
                session_regenerate_id(true);

                // 3. セッションに情報を保存
                $_SESSION['team_id'] = $team['id'];        // どのチームか
                $_SESSION['team_name'] = $team['team_name']; // 画面表示用
                $_SESSION['role'] = $is_admin ? 'admin' : 'user'; // 権限

                header("Location: ../team/team_admin.php");
                exit;
            } else {
                $error = "パスワードが正しくありません。";
            }
        } else {
            $error = "チーム名が見つかりませんでした。";
        }
    } else {
        $error = "チーム名とパスワードを入力してください。";
    }
}

// セレクトボックス用にチーム一覧を取得
$teams_stmt = $pdo->query("SELECT team_name FROM teams ORDER BY id DESC");
$team_list = $teams_stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン | チーム・メンバー管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .login-card { border: none; border-radius: 0.5rem; }
        .card-header { font-weight: bold; }
        .navbar-brand { font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-5">
    <div class="container justify-content-center">
        <span class="navbar-brand">チーム・メンバー管理システム</span>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            
            <div class="card shadow-sm login-card">
                <div class="card-header bg-white text-center py-3">
                    ログインして管理を開始
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger small p-2">
                            <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">チーム名</label>
                            <select name="team_name" class="form-select mb-2" required>
                                <option value="">所属チームを選択</option>
                                <?php foreach ($team_list as $t): ?>
                                    <option value="<?= htmlspecialchars($t['team_name']) ?>">
                                        <?= htmlspecialchars($t['team_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">パスワード</label>
                            <input type="password" name="password" class="form-control mb-2" placeholder="パスワードを入力" required>
                            <div class="form-text" style="font-size: 0.75rem;">
                                管理者または一般用パスワードを入力してください。
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100 py-2">
                            ログイン
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>

</body>
</html>