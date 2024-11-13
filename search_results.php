<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページタイトル</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/serch_results.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php
    include './cummon/header.php';
    require './cummon/config.php';

    // 1ページ当たりの表示件数
    $limit = 20;
    // 現在のページ番号を取得
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    // データのオフセット
    $offset = ($page - 1) * $limit;


    // 検索クエリを取得
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';

    $searchResults = [];

    if (!empty($query)) {
        $stmt = $pdo->prepare("
            SELECT community_id, community_name, community_image 
            FROM communities 
            WHERE community_name LIKE :query 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':query', "%$query%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // 総データ数を取得
    if (!empty($query)) {
        $totalQuery = $pdo->prepare("SELECT COUNT(*) FROM communities WHERE community_name LIKE :query");
        $totalQuery->execute([':query' => "%$query%"]);
        $totalRecords = $totalQuery->fetchColumn();
        $totalPages = ceil($totalRecords / $limit);
    }

    ?>


    <div class="content">
        <h2>「<?= htmlspecialchars($query) ?>」の検索結果</h2>

        <?php if (empty($searchResults)): ?>
            <p>該当する結果が見つかりませんでした。</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($searchResults as $result): ?>
                    <div class="col-6 col-lg-3 p-2"><a href="./talk.php?community_id=<?= htmlspecialchars($result['community_id']); ?>">
                            <div class="anime-item-wrapper">
                                <div class="anime-item">
                                    <img src="<?= htmlspecialchars($result['community_image']); ?>" alt="<?= htmlspecialchars($result['community_name']); ?>">
                                    <div class="anime-title-wrapper">
                                        <p class="anime-title"><?= htmlspecialchars($result['community_name']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </a></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

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
                        <a class="page-link" href="?page=<?= $page - 1; ?>&query=<?= urlencode($query); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- ページ番号 -->
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>&query=<?= urlencode($query); ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <!-- 次へボタン -->
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1; ?>&query=<?= urlencode($query); ?>" aria-label="Next">
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