<?php
session_start();

// 1. セッション変数をすべて空にする
$_SESSION = array();

// 2. ブラウザのクッキーに保存されているセッションIDも無効化する
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// 3. サーバー側のセッションデータを破棄する
session_destroy();

// 4. ログイン画面へリダイレクト
header("Location: ../login/login.php");
exit;