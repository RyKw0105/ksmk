<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページタイトル</title>
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- ヘッダーをインクルード -->
<?php include './cummon/header.php'; ?> 


<div class="content">
    <?php
    // 他のファイル example.php から接続を呼び出す
    require 'config.php';

    // ここからデータベースに接続済みの状態でクエリを実行できます
    $query = $pdo->query("SELECT * FROM users");
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        echo "User ID: " . $row['user_id'] . " - Name: " . $row['user_name'] . "<br>";
    }
    ?>

</div>

<!-- フッターをインクルード -->
<?php include './cummon/footer.php'; ?> 

</body>
</html>