<?php
// db_connect.php

// データベース接続情報
$host = 'localhost';
$dbname = 'anime';
$user = 'root';
$password = 'ryosuke';

try {
    // PDOインスタンスを作成して接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    // エラーモードを例外に設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // 接続エラーの際にエラーメッセージを表示してスクリプトを終了
    die("データベース接続エラー: " . $e->getMessage());
}