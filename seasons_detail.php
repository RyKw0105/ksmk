<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページタイトル</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/lists.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php
    session_start();
    include './cummon/header.php';
    require './cummon/config.php';

    // 1ページ当たりの表示件数
    $limit = 20;
    // 現在のページ番号を取得
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    // データのオフセット
    $offset = ($page - 1) * $limit;

    // URLパラメータから season_id を取得
    $season_id = isset($_GET['season']) ? (int)$_GET['season'] : 1;

    // 総データ数を取得
    $totalQuery = $pdo->prepare("SELECT COUNT(*) FROM communities WHERE season_id = :season_id");
    $totalQuery->execute(['season_id' => $season_id]);
    $totalRecords = $totalQuery->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    // アニメ一覧の取得
    $query = $pdo->prepare("SELECT * FROM communities WHERE season_id = :season_id LIMIT :limit OFFSET :offset");
    $query->bindValue(':season_id', $season_id, PDO::PARAM_INT);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    // シーズン名の取得
    $seasonQuery = $pdo->prepare("SELECT season_name FROM seasons WHERE season_id = :season_id");
    $seasonQuery->execute(['season_id' => $season_id]);
    $seasonResult = $seasonQuery->fetch(PDO::FETCH_ASSOC);
    ?>

    <!-- コンテンツ -->
    <div class="content anime-gallery container">
        <h2 class="mb-4 genre-title"><span><?= htmlspecialchars($seasonResult['season_name']); ?>アニメ</span></h2>

        <!-- 作品 -->
        <div class="row">
            <?php foreach ($results as $community): ?>
                <div class="col-6 col-lg-3 p-2"><a href="./talk.php?community_id=<?= htmlspecialchars($community['community_id']); ?>">
                    <div class="anime-item-wrapper">
                        <div class="anime-item">
                            <img src="<?= htmlspecialchars($community['community_image']); ?>" alt="<?= htmlspecialchars($community['community_name']); ?>">
                            <div class="anime-title-wrapper">
                                <p class="anime-title"><?= htmlspecialchars($community['community_name']); ?></p>
                            </div>
                        </div>
                    </div>
                    </a></div>
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
                        <a class="page-link" href="?page=<?= $page - 1; ?>&season=<?= $season_id; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- ページ番号 -->
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>&season=<?= $season_id; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1; ?>&season=<?= $season_id; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php include './cummon/footer.php'; ?>

</body>

</html>