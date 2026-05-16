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

$stmt = $pdo->prepare(
    'SELECT r.*, u.username as creator_name, 
            (SELECT COUNT(*) FROM room_members WHERE room_id = r.id) as member_count,
            (SELECT COUNT(*) FROM media WHERE id IN 
                (SELECT id FROM media m 
                 JOIN galleries g ON m.gallery_id = g.id 
                 WHERE g.id IN (SELECT id FROM galleries WHERE user_id IN 
                    (SELECT user_id FROM room_members WHERE room_id = r.id)))) as item_count
     FROM rooms r 
     JOIN users u ON r.creator_id = u.id 
     WHERE r.is_public = TRUE 
     ORDER BY r.created_at DESC'
);
$stmt->execute();
$rooms = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'data' => $rooms
]);
?>