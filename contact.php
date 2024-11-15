<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページタイトル</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/contact.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php
    include './cummon/header.php';
    require './cummon/config.php';
    ?>


<div class="contact-form-container">
    <h2>お問い合わせ</h2>
    <form>
        <div class="form-group">
            <label for="name">お名前</label>
            <input type="text" class="form-control" id="name" placeholder="お名前を入力してください" required>
        </div>
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" class="form-control" id="email" placeholder="メールアドレスを入力してください" required>
        </div>
        <div class="form-group">
            <label for="subject">件名</label>
            <input type="text" class="form-control" id="subject" placeholder="件名を入力してください" required>
        </div>
        <div class="form-group">
            <label for="message">お問い合わせ内容</label>
            <textarea class="form-control" id="message" rows="5" placeholder="お問い合わせ内容を入力してください" required></textarea>
        </div>
        <button type="submit" class="submit-button">送信</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php include './cummon/footer.php'; ?>

</body>

</html>