<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($genre['genre_name']) ?>の詳細</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/lists.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<?php
session_start();
include './cummon/header.php';
require './cummon/config.php';

// genre_id を取得
$genre_id = isset($_GET['genre_id']) ? (int)$_GET['genre_id'] : 0;

if ($genre_id === 0) {
    die("ジャンルIDが指定されていません。");
}

// ジャンル情報の取得
$genreQuery = $pdo->prepare("SELECT genre_name FROM genres WHERE genre_id = :genre_id");
$genreQuery->execute([':genre_id' => $genre_id]);
$genre = $genreQuery->fetch(PDO::FETCH_ASSOC);

if (!$genre) {
    die("指定されたジャンルが存在しません。");
}

// コミュニティ情報の取得
$communityQuery = $pdo->prepare("SELECT 
    c.community_id, 
    c.community_name, 
    c.community_image 
FROM 
    communities_genres cg
INNER JOIN 
    communities c ON cg.community_id = c.community_id
WHERE 
    cg.genre_id = :genre_id");
$communityQuery->execute([':genre_id' => $genre_id]);
$communities = $communityQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<body>
    <div class="content container anime-gallery">
        <h2 class="mb-4 genre-title"><span><?= htmlspecialchars($genre['genre_name']) ?></span></h2>

        <div class="row">
            <?php foreach ($communities as $community): ?>
                <div class="col-6 col-lg-3 p-2">
                    <a href="./community.php?community_id=<?= htmlspecialchars($community['community_id']) ?>">
                        <div class="card">
                            <div class="anime-item-wrapper">
                                <div class="anime-item">
                                    <img src="<?= htmlspecialchars($community['community_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($community['community_name']) ?>">
                                    <div class="anime-title-wrapper">
                                        <p class="anime-title"><?= htmlspecialchars($community['community_name']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include './cummon/footer.php'; ?>
</body>

</html>