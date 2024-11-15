<?php
session_start(); // セッションを開始

// ユーザーがログインしているかチェック
if (!isset($_SESSION['user_id'])) {
    // ログインしていない場合、ログインページにリダイレクト
    header("Location: login.php");
    exit;
}

// ログイン中のユーザーIDを取得
$user_id = $_SESSION['user_id'];
?>
