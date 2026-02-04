<?php
require_once('db_config.php');

// 設定したいパスワード
$admin_plain = 'admin123'; // 管理者用
$user_plain  = 'user123';  // 一般用

// ハッシュ化
$admin_hashed = password_hash($admin_plain, PASSWORD_DEFAULT);
$user_hashed  = password_hash($user_plain, PASSWORD_DEFAULT);

// 特定のチーム（例: IDが1のチーム）のパスワードを更新
$sql = "UPDATE teams SET admin_password = ?, user_password = ? WHERE id = 2";
$stmt = $pdo->prepare($sql);
$stmt->execute([$admin_hashed, $user_hashed]);

echo "パスワードをハッシュ化して更新しました。";
?>