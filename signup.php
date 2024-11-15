<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページタイトル</title>
    <link rel="stylesheet" href="./css/signin.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="header">
        <div class="app-name">
            <a href="../index.php">アプリ名</a>
        </div>
    </div>

    <?php
    require './cummon/config.php';

    $username = '';
    // フォームが送信されたか確認
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // エラーメッセージを格納する配列
        $errors = [];

        // 入力検証: 空の入力をチェック
        if (empty($username) || empty($password) || empty($confirm_password)) {
            $errors[] = "全てのフィールドを入力してください。";
        }

        // ユーザー名の形式を検証
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = "ユーザー名は英数字とアンダースコア(_)のみ使用できます。";
        }

        // パスワードが一致しているか確認
        if ($password !== $confirm_password) {
            $errors[] = "パスワードが一致しません。";
        }

        // エラーがない場合はDBに登録
        if (empty($errors)) {
            try {
                // ユーザー名が既に存在するか確認
                $checkQuery = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_name = :username");
                $checkQuery->execute([':username' => $username]);

                if ($checkQuery->fetchColumn() > 0) {
                    $errors[] = "このユーザー名は既に使用されています。";
                } else {
                    // パスワードをハッシュ化
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // ユーザー情報を挿入
                    $insertQuery = $pdo->prepare("
                        INSERT INTO users (user_name, password, created_date, updated_date)
                        VALUES (:username, :password, NOW(), NOW())
                    ");
                    $insertQuery->execute([
                        ':username' => $username,
                        ':password' => $hashedPassword,
                    ]);
                    // リダイレクト
                    header("Location: login.php");
                }
            } catch (Exception $e) {
                $errors[] = "エラーが発生しました: " . $e->getMessage();
            }
        }
    }
    ?>

    <div class="content">
        <div class="register-box">
            <h2>アカウント作成</h2>

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
                <div class="form-group">
                    <label for="confirm-password">パスワードを再入力</label>
                    <input type="password" class="form-control" id="confirm-password" name="confirm_password" placeholder="パスワードを再入力" required>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">作成</button>
                    <a class="login-link" href="./login.php">ログインはこちら</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>