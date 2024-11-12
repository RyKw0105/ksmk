    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ページタイトル</title>
        <link rel="stylesheet" href="./css/style.css">
        <link rel="stylesheet" href="./css/index.css">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body>

        <?php include './cummon/header.php'; ?>


        <div class="content">
            <?php
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
        WHERE seasons.season_id = 3
    ");
            $query_season->execute();
            $results_season = $query_season->fetchAll(PDO::FETCH_ASSOC);
            ?>


            <div class="ai-chat">
                <h2>アニメの事ならAIチャットに！</h2>
                <p>自分に合う作品を知りたい！<br>
                    この2000年代の人気のアニメを知りたい！<br>
                    そんなことならAIチャットにお任せ！</p>
                <div class="button">
                    <a href="#">AIと話す</a>
                </div>
            </div>
            <div class="title">
                <h2><span>～好きなコミュニティで語ろう～</span></h2>
            </div>
            <!-- 今季のアニメセクション -->
            <div class="section anime-season">
                <div class="section-title">今季のアニメ<br>〜2024年秋アニメ〜</div>
                <div class="content-grid">
                    <div class="content-item">
                        <a href="/talk.php?community_id=<?= htmlspecialchars($results_season[0]['season_recomend1_id']) ?>">
                            <img src="<?= htmlspecialchars($results_season[0]['season_recomend1_image']) ?>" alt="アニメA">
                            <p><?= htmlspecialchars($results_season[0]['season_recomend1_name']) ?></p>
                        </a>
                    </div>
                    <div class="content-item">
                        <a href="/talk.php?community_id=<?= htmlspecialchars($results_season[0]['season_recomend2_id']) ?>">
                            <img src="<?= htmlspecialchars($results_season[0]['season_recomend2_image']) ?>" alt="アニメB">
                            <p><?= htmlspecialchars($results_season[0]['season_recomend2_name']) ?></p>
                        </a>
                    </div>
                    <div class="content-item">
                        <a href="/talk.php?community_id=<?= htmlspecialchars($results_season[0]['season_recomend3_id']) ?>">
                            <img src="<?= htmlspecialchars($results_season[0]['season_recomend3_image']) ?>" alt="アニメC">
                            <p><?= htmlspecialchars($results_season[0]['season_recomend3_name']) ?></p>
                        </a>
                    </div>
                    <div class="content-item">
                        <a href="/talk.php?community_id=<?= htmlspecialchars($results_season[0]['season_recomend4_id']) ?>">
                            <img src="<?= htmlspecialchars($results_season[0]['season_recomend4_image']) ?>" alt="アニメD">
                            <p><?= htmlspecialchars($results_season[0]['season_recomend4_name']) ?></p>
                        </a>
                    </div>
                </div>
                <div class="more-button"><a href="/lists.php?season=3">もっと見る</a></div>
            </div>

            <!-- 放送時期別のアニメ -->
            <div class="section anime-by-season">
                <div class="section-title">放送時期別にアニメを探す</div>
                <div class="content-grid">
                    <div class="content-item">
                        <p>夏アニメ一覧</p>
                    </div>
                    <div class="content-item">
                        <p>秋アニメ一覧</p>
                    </div>
                    <div class="content-item">
                        <p>春アニメ一覧</p>
                    </div>
                    <div class="content-item">
                        <p>冬アニメ一覧</p>
                    </div>
                </div>
                <div class="more-button"><a href="#">もっと見る</a></div>
            </div>

            <!-- 制作会社セクション -->
            <div class="section production-companies">
                <div class="section-title">制作会社</div>
                <div class="content-grid">
                    <div class="content-item">
                        <img src="kyoto_animation.jpg" alt="Kyoto Animation">
                        <p>Kyoto Animation</p>
                    </div>
                    <div class="content-item">
                        <img src="a1_pictures.jpg" alt="A-1 Pictures">
                        <p>A-1 Pictures</p>
                    </div>
                    <div class="content-item">
                        <img src="a1_pictures.jpg" alt="A-1 Pictures">
                        <p>A-1 Pictures</p>
                    </div>
                    <div class="content-item">
                        <img src="a1_pictures.jpg" alt="A-1 Pictures">
                        <p>A-1 Pictures</p>
                    </div>
                </div>
                <div class="more-button"><a href="#">もっと見る</a></div>
            </div>

        </div>

        <?php include './cummon/footer.php'; ?>

    </body>

    </html>