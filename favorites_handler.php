<?php
require './cummon/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['community_id']) || !isset($data['user_id'])) {
    echo json_encode(['success' => false, 'message' => '無効なリクエスト']);
    exit;
}

$community_id = (int) $data['community_id'];
$user_id = (int) $data['user_id'];

try {
    // ユーザーがお気に入りに追加しているか確認
    $checkQuery = $pdo->prepare("SELECT COUNT(*) AS count FROM favorites WHERE community_id = :community_id AND user_id = :user_id");
    $checkQuery->execute(['community_id' => $community_id, 'user_id' => $user_id]);
    $isFavorited = $checkQuery->fetch(PDO::FETCH_ASSOC)['count'] > 0;

    if ($isFavorited) {
        // お気に入りを削除
        $deleteQuery = $pdo->prepare("DELETE FROM favorites WHERE community_id = :community_id AND user_id = :user_id");
        $deleteQuery->execute(['community_id' => $community_id, 'user_id' => $user_id]);
    } else {
        // お気に入りを追加
        $insertQuery = $pdo->prepare("INSERT INTO favorites (community_id, user_id, favorite_date) VALUES (:community_id, :user_id, NOW())");
        $insertQuery->execute(['community_id' => $community_id, 'user_id' => $user_id]);
    }

    // 最新の「お気に入り数」を取得
    $countQuery = $pdo->prepare("SELECT COUNT(*) AS favorite_count FROM favorites WHERE community_id = :community_id");
    $countQuery->execute(['community_id' => $community_id]);
    $favoriteCount = $countQuery->fetch(PDO::FETCH_ASSOC)['favorite_count'];

    echo json_encode(['success' => true, 'favorite_count' => $favoriteCount]);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
