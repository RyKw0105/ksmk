<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページタイトル</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/mypage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php
    include './cummon/auth_check.php';
    include './cummon/header.php';
    require './cummon/config.php';

    // 1ページ当たりの表示件数
    $limit = 10;
    // 現在のページ番号を取得
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    // データのオフセット
    $offset = ($page - 1) * $limit;

    // ユーザー情報の取得
    $userQuery = $pdo->prepare("SELECT user_name, profile_photo FROM users WHERE user_id = :user_id");
    $userQuery->execute(['user_id' => $user_id]);
    $userResult = $userQuery->fetch(PDO::FETCH_ASSOC);

    // データが取得できなかった場合のエラーハンドリング
    if (!$userResult) {
        die("ユーザー情報が見つかりません。");
    }

    // 総データ数を取得
    $totalQuery = $pdo->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = :user_id");
    $totalQuery->execute(['user_id' => $user_id]);
    $totalRecords = $totalQuery->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    // お気に入りの取得
    $favorite_query = $pdo->prepare("
        SELECT  f.favorite_id,
                f.favorite_date, 
                c.community_id,
                c.community_name,
                c.community_image
        FROM 
            favorites f
        INNER JOIN 
            communities c ON f.community_id = c.community_id
        WHERE
            f.user_id = :user_id
        LIMIT :limit
        OFFSET :offset
    ");
    $favorite_query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $favorite_query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $favorite_query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $favorite_query->execute();
    $favorite = $favorite_query->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <div class="content container mt-5">
        <div class="row gallery">
            <!-- 左側のユーザー情報 -->
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <img src="<?= htmlspecialchars($userResult['profile_photo']); ?>" alt="アイコン" class="rounded-circle mb-3">
                        <h5 class="card-title"><?= htmlspecialchars($userResult['user_name']); ?></h5>
                        <button class="btn btn-outline-primary btn-sm mt-3">アカウント設定</button>
                    </div>
                </div>
            </div>

            <!-- 右側の棚部分 -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header text-center bg-primary text-white">
                        <h4 class="mb-0">お気に入り棚</h4>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <?php if (empty($favorite)) { ?>
                                <div class="no-favorite-container">
                                    <p class="no-favorite-message">お気に入りがありません。</p>
                                </div>
                            <?php } ?>

                            <?php foreach ($favorite as $results): ?>
                                <div class="col-6 col-md-4">
                                    <div class="shelf-item">
                                        <a href="./talk.php?community_id=<?= htmlspecialchars($results['community_id']); ?>">
                                            <img src="<?= htmlspecialchars($results['community_image']); ?>" alt="作品1">
                                            <p><?= htmlspecialchars($results['community_name']); ?></p>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- ページネーション -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php
                                // 最大5ページのみ表示
                                $maxPagesToShow = 5;

                                // 開始と終了のページ番号を計算
                                $startPage = max(1, $page - floor($maxPagesToShow / 2));
                                $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);

                                // 最後のページが最大ページ数に収まらない場合の調整
                                if ($endPage - $startPage < $maxPagesToShow - 1) {
                                    $startPage = max(1, $endPage - $maxPagesToShow + 1);
                                }
                                ?>

                                <!-- 戻るボタン -->
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <!-- ページ番号 -->
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <!-- 次へボタン -->
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>


                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php include './cummon/footer.php'; ?>

</body>

</html>