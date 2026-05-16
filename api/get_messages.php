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
offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$stmt = $pdo->prepare(
    'SELECT * FROM chat_messages 
     WHERE is_private = FALSE OR private_recipient_id = ? OR user_id = ? 
     ORDER BY created_at DESC 
     LIMIT ? OFFSET ?'
);
$stmt->execute([$user_id, $user_id, $limit, $offset]);
$messages = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'data' => array_reverse($messages)
]);
?>