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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$is_public = isset($_POST['is_public']) ? (bool)$_POST['is_public'] : true;
$password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

if (empty($name)) {
    http_response_code(400);
    echo json_encode(['error' => 'Room name is required']);
    exit();
}

$stmt = $pdo->prepare(
    'INSERT INTO rooms (name, description, creator_id, is_public, password) 
     VALUES (?, ?, ?, ?, ?)'
);

if ($stmt->execute([$name, $description, $user_id, $is_public, $password])) {
    $room_id = $pdo->lastInsertId();
    
    // Add creator as member
    $member_stmt = $pdo->prepare('INSERT INTO room_members (room_id, user_id) VALUES (?, ?)');
    $member_stmt->execute([$room_id, $user_id]);
    
    echo json_encode([
        'success' => true,
        'room_id' => $room_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create room']);
}
?>