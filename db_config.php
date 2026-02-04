<?php
// データベース接続設定
$db_host = "localhost";
$db_name = "gs_kadai10";
$db_user = "root";
$db_pass = "";

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8", $db_user, $db_pass);
} catch (PDOException $e) {
    exit('DB Connection Error:' . $e->getMessage());
}