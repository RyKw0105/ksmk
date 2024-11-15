<?php
require '../cummon/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    try {
        // ユーザー名が既に存在するか確認
        $checkQuery = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_name = :username");
        $checkQuery->execute([':username' => $username]);
        if ($checkQuery->fetchColumn() > 0) {
            die("このユーザー名は既に使用されています。");
        }

        // パスワードをハッシュ化
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // データベースに新規ユーザーを挿入
        $insertQuery = $pdo->prepare("
            INSERT INTO users (user_name, password, created_date, updated_date)
            VALUES (:username, :password, NOW(), NOW())
        ");
        $insertQuery->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
        ]);

        // 成功メッセージとリダイレクト
        header("Location: login.php?signup=success");
        exit;

    } catch (Exception $e) {
        die("エラーが発生しました: " . $e->getMessage());
    }
}
