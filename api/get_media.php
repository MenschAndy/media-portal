<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Get media from user's galleries
$stmt = $pdo->prepare(
    'SELECT m.*, u.username FROM media m 
     JOIN galleries g ON m.gallery_id = g.id 
     JOIN users u ON m.user_id = u.id 
     WHERE g.user_id = ? OR g.is_public = TRUE 
     ORDER BY m.created_at DESC 
     LIMIT ? OFFSET ?'
);
$stmt->execute([$user_id, $limit, $offset]);
$media = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'data' => $media
]);
?>