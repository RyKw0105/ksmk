<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページタイトル</title>
    <link rel="stylesheet" href="./css/signin.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<?php
session_start();
require './cummon/config.php';

$username = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 入力チェック
    if (empty($username) || empty($password)) {
        $errors[] = "全てのフィールドを入力してください。";
    }

    if (empty($errors)) {
        try {
            // ユーザー名をもとにデータベースから情報を取得
            $query = $pdo->prepare("SELECT user_id, user_name, password FROM users WHERE user_name = :username");
            $query->execute([':username' => $username]);
            $user = $query->fetch(PDO::FETCH_ASSOC);

            // ユーザーが存在し、パスワードが一致するか確認
            if ($user && password_verify($password, $user['password'])) {
                // セッションにユーザー情報を保存
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];

                // リダイレクト
                header("Location: index.php?login=success");
                exit;
            } else {
                $errors[] = "ユーザー名またはパスワードが間違っています。";
            }
        } catch (Exception $e) {
            $errors[] = "エラーが発生しました: " . $e->getMessage();
        }
    }
}
?>

<body>
    <div class="header">
        <div class="app-name">
            <a href="../index.php">アプリ名</a>
        </div>
    </div>


    <div class="content">
        <div class="login-box">
            <h2>ログイン</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($username) ?>" placeholder="ユーザー名を入力" required>
                </div>
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="パスワードを入力" required>
                </div>
                <button type="submit" class="btn btn-primary">ログイン</button>
                <a class="register-link" href="./signin.php">新規登録はこちら</a>
            </form>
        </div>
    </div>
</body>

</html>