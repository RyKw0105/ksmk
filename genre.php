<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ジャンルページ</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/seasons.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php
    session_start();
    include './cummon/header.php';
    require './cummon/config.php';

    // ジャンルを取得
    $genreQuery = $pdo->query("SELECT genre_id, genre_name FROM genres");
    $genres = $genreQuery->fetchAll(PDO::FETCH_ASSOC);

    // ジャンルごとのレコメンド情報を取得
    $communityQuery = $pdo->prepare("SELECT 
             c1.community_id AS recommend1_id,
             c1.community_name AS recommend1_name,
             c1.community_image AS recommend1_image,
             c2.community_id AS recommend2_id,
             c2.community_name AS recommend2_name,
             c2.community_image AS recommend2_image,
             c3.community_id AS recommend3_id,
             c3.community_name AS recommend3_name,
             c3.community_image AS recommend3_image,
             c4.community_id AS recommend4_id,
             c4.community_name AS recommend4_name,
             c4.community_image AS recommend4_image
         FROM genres g
         LEFT JOIN communities c1 ON g.genre_recommend1 = c1.community_id
         LEFT JOIN communities c2 ON g.genre_recommend2 = c2.community_id
         LEFT JOIN communities c3 ON g.genre_recommend3 = c3.community_id
         LEFT JOIN communities c4 ON g.genre_recommend4 = c4.community_id
         WHERE g.genre_id = :genre_id
         LIMIT 1");
    ?>

    <div class="content anime-gallery container">
        <?php foreach ($genres as $genre): ?>
            <h2 class="title">
                <a href="./genre_detail.php?genre_id=<?= htmlspecialchars($genre['genre_id']) ?>">
                    <?= htmlspecialchars($genre['genre_name']) ?>
                </a>
            </h2>

            <?php
            // ジャンルごとのレコメンド情報を取得
            $communityQuery->execute(['genre_id' => $genre['genre_id']]);
            $recommendations = $communityQuery->fetch(PDO::FETCH_ASSOC);
            ?>

            <div class="row">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <?php if (!empty($recommendations["recommend{$i}_id"])): ?>
                        <div class="col-md-3">
                            <a href="./talk.php?community_id=<?= htmlspecialchars($recommendations["recommend{$i}_id"]) ?>">
                                <div class="anime-item-wrapper">
                                    <div class="anime-item">
                                        <img src="<?= htmlspecialchars($recommendations["recommend{$i}_image"]) ?>" class="card-img-top" alt="<?= htmlspecialchars($recommendations["recommend{$i}_name"]) ?>">
                                        <div class="anime-title-wrapper">
                                            <p class="anime-title"><?= htmlspecialchars($recommendations["recommend{$i}_name"]) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php include './cummon/footer.php'; ?>
</body>

</html>