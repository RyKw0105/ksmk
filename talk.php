<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページタイトル</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/talk.css">
    <script src="./js/talk.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php
    include './cummon/header.php';
    require './cummon/config.php';

    $community_id = isset($_GET['community_id']) ? (int)$_GET['community_id'] : 1;
    $user_id = 1;

    //投稿の表示
    $postQuery = $pdo->prepare("
        SELECT posts.post_id,
            posts.post_content,
            posts.photo,
            posts.posted_date,
            posts.delete_flag,
            u.user_id,
            u.user_name,
            u.profile_photo AS user_photo
        FROM posts
        LEFT JOIN communities AS c ON posts.community_id = c.community_id
        LEFT JOIN users AS u ON posts.user_id = u.user_id
        WHERE posts.community_id = :community_id AND posts.delete_flag = 0
    ");
    $postQuery->execute(['community_id' => $community_id]);
    $postResults = $postQuery->fetchAll(PDO::FETCH_ASSOC);

    //コミュニティ情報の取得
    $communityQuery = $pdo->query("SELECT * FROM communities WHERE community_id = $community_id");
    $communityResult = $communityQuery->fetch(PDO::FETCH_ASSOC);
    $communityName = $communityResult['community_name'] ?? 'コミュニティ名が未設定';


    //投稿の追加、削除
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 投稿の削除処理
        if (isset($_POST['delete']) && isset($_POST['delete_post_id'])) {
            $delete_post_id = (int)$_POST['delete_post_id'];

            // delete_flag を 1 に更新
            $deleteQuery = $pdo->prepare("UPDATE posts SET delete_flag = 1 WHERE post_id = :post_id");
            $deleteQuery->execute([':post_id' => $delete_post_id]);

            // ページをリロードして削除後の状態を表示
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit;
        }

        // 投稿の追加処理
        if (isset($_POST['submit'])) {
            $post_content = $_POST['post_content'];
            $photo = '';

            // ファイルのアップロード処理
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDate = date('YmdHis'); // YYYYMMDDHHMMSS形式
                $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photo = './uploads/' . $uploadDate . '.' . $extension;
                move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
            }

            // データベースに挿入
            $insertQuery = $pdo->prepare("
                INSERT INTO posts (community_id, user_id, post_content, photo, posted_date, delete_flag)
                VALUES (:community_id, :user_id, :post_content, :photo, NOW(), 0)
            ");
            $insertQuery->execute([
                ':community_id' => $community_id,
                ':user_id' => $user_id,
                ':post_content' => $post_content,
                ':photo' => $photo,
            ]);

            // ページをリロードして新しい投稿を表示
            header("Location: {$_SERVER['REQUEST_   URI']}");
            exit;
        }
    }


    // お気に入り数の取得
    $favoritesQuery = $pdo->prepare("SELECT COUNT(*) AS favorite_count FROM favorites WHERE community_id = :community_id");
    $favoritesQuery->execute(['community_id' => $community_id]);
    $favoritesCount = $favoritesQuery->fetch(PDO::FETCH_ASSOC)['favorite_count'] ?? 0;

    //ユーザーのお気に入り情報を取得
    $userFavoriteQuery = $pdo->prepare("SELECT COUNT(*) AS user_favorited FROM favorites WHERE community_id = :community_id AND user_id = :user_id");
    $userFavoriteQuery->execute(['community_id' => $community_id, 'user_id' => $user_id]);
    $userFavorited = $userFavoriteQuery->fetch(PDO::FETCH_ASSOC)['user_favorited'] > 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'favorite') {
            // お気に入りに追加
            $insertQuery = $pdo->prepare("INSERT INTO favorites (community_id, user_id, favorite_date) VALUES (:community_id, :user_id, NOW())");
            $insertQuery->execute(['community_id' => $community_id, 'user_id' => $user_id]);
            echo json_encode(['success' => true, 'message' => 'お気に入りに追加しました。']);
        } elseif ($action === 'unfavorite') {
            // お気に入りから削除
            $deleteQuery = $pdo->prepare("DELETE FROM favorites WHERE community_id = :community_id AND user_id = :user_id");
            $deleteQuery->execute(['community_id' => $community_id, 'user_id' => $user_id]);
            echo json_encode(['success' => true, 'message' => 'お気に入りを解除しました。']);
        }
        exit;
    }
    ?>

    <div class="content">
        <!-- いいねボタン -->
        <button class="goodBtn <?= $userFavorited ? 'active' : '' ?>"
            type="button"
            data-community-id="<?= $community_id ?>"
            data-user-id="<?= $user_id ?>">
            <i class="fa-solid fa-heart"></i>
            <span class="donut"></span>
            <div class="bubble">
                <span></span><span></span><span></span>
                <span></span><span></span><span></span>
                <span></span><span></span>
            </div>
            <p class="like-count"><?= $favoritesCount ?></p>
        </button>

        <div class="chat-container">
            <!-- ヘッダー -->
            <div class="chat-header">
                <div class="chat-header-name"><?= htmlspecialchars($communityResult['community_name']) ?></div>
            </div>


            <!-- メッセージエリア -->
            <div class="chat-body">
                <div class="chat-body-inner">
                    <?php if (empty($postResults)): ?>
                        <p class="no-posts-message">投稿がありません。<br>是非初めての投稿をしてみましょう！</p>
                    <?php else: ?>
                        <?php foreach ($postResults as $post): ?>
                            <?php if ($post['delete_flag'] == 0): ?> <!-- delete_flagが0の投稿のみ表示 -->
                                <?php if ($user_id == $post['user_id']): ?>
                                    <div class="message sent">
                                        <img src="<?= htmlspecialchars($post['user_photo']) ?>" alt="ユーザー写真" class="avatar">
                                        <div class="content_message">
                                            <strong><?= htmlspecialchars($post['user_name']) ?></strong>
                                            <?php if (!empty($post['photo'])): ?>
                                                <img src="<?= htmlspecialchars($post['photo']) ?>" alt="投稿写真" class="post-photo">
                                            <?php endif; ?>
                                            <p><?= nl2br(htmlspecialchars($post['post_content'])) ?></p>
                                            <span class="timestamp"><?= htmlspecialchars($post['posted_date']) ?></span>

                                            <form action="" method="POST" style="display: inline;">
                                                <input type="hidden" name="delete_post_id" value="<?= $post['post_id'] ?>">
                                                <button type="submit" name="delete" class="delete-button">削除</button>
                                            </form>

                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="message received">
                                        <img src="<?= htmlspecialchars($post['user_photo']) ?>" alt="ユーザー写真" class="avatar">
                                        <div class="content_message">
                                            <strong><?= htmlspecialchars($post['user_name']) ?></strong>
                                            <?php if (!empty($post['photo'])): ?>
                                                <img src="<?= htmlspecialchars($post['photo']) ?>" alt="投稿写真" class="post-photo">
                                            <?php endif; ?>
                                            <p><?= nl2br(htmlspecialchars($post['post_content'])) ?></p>
                                            <span class="timestamp"><?= htmlspecialchars($post['posted_date']) ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 入力エリア -->
            <div class="chat-footer">
                <form action="" method="POST" enctype="multipart/form-data">
                    <textarea name="post_content" placeholder="テキストを入力" rows="3" required></textarea>
                    <label for="file-input" class="file-input-label">
                        📷
                        <input type="file" id="file-input" name="photo" accept="image/*" onchange="previewImage(event)">
                    </label>
                    <div class="image-preview-container">
                        <img id="preview" src="" alt="画像プレビュー" class="image-preview">
                        <button type="button" class="round_btn" id="cancel-button" onclick="cancelImage()"></button>
                    </div>
                    <button type="submit" name="submit">送信</button>
                </form>
            </div>
        </div>
    </div>
    <?php include './cummon/footer.php'; ?>

    <script src="./js/talk.js"></script>
</body>

</html>