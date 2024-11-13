<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ページタイトル</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/seasons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php
    include './cummon/header.php';
    require './cummon/config.php';

    $query_season = $pdo->prepare("
        SELECT seasons.*, 
        c1.community_id AS season_recomend1_id, 
        c1.community_name AS season_recomend1_name, 
        c1.community_image AS season_recomend1_image,
        c2.community_id AS season_recomend2_id, 
        c2.community_name AS season_recomend2_name, 
        c2.community_image AS season_recomend2_image,
        c3.community_id AS season_recomend3_id, 
        c3.community_name AS season_recomend3_name, 
        c3.community_image AS season_recomend3_image,
        c4.community_id AS season_recomend4_id, 
        c4.community_name AS season_recomend4_name, 
        c4.community_image AS season_recomend4_image
        FROM seasons
        LEFT JOIN communities AS c1 ON seasons.season_recomend1 = c1.community_id
        LEFT JOIN communities AS c2 ON seasons.season_recomend2 = c2.community_id
        LEFT JOIN communities AS c3 ON seasons.season_recomend3 = c3.community_id
        LEFT JOIN communities AS c4 ON seasons.season_recomend4 = c4.community_id
    ");
    $query_season->execute();
    $results_season = $query_season->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="content anime-gallery container">
        <?php foreach ($results_season as $seasons): ?>
            <h2 class="title"><a href="./lists.php?season=<?= htmlspecialchars($seasons['season_id']) ?>"><?= htmlspecialchars($seasons['season_name']); ?></a></h2>
            <div class="row">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <?php
                    $imageKey = "season_recomend{$i}_image";
                    $nameKey = "season_recomend{$i}_name";
                    $idKey = "season_recomend{$i}_id";
                    ?>
                    <?php if (!empty($seasons[$imageKey]) && !empty($seasons[$nameKey])): ?>
                        <div class="col-6 col-lg-3 p-2">
                            <a href="./talk.php?community_id=<?= htmlspecialchars($seasons[$idKey]); ?>">
                                <div class="anime-item-wrapper">
                                    <div class="anime-item">
                                        <img src="<?= htmlspecialchars($seasons[$imageKey]); ?>"
                                            alt="<?= htmlspecialchars($seasons[$nameKey]); ?>">
                                        <div class="anime-title-wrapper">
                                            <p class="anime-title"><?= htmlspecialchars($seasons[$nameKey]); ?></p>
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