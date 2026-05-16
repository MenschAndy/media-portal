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
$username = $_SESSION['username'];
$message = $_POST['message'] ?? '';
$is_private = isset($_POST['is_private']) ? (bool)$_POST['is_private'] : false;
$private_recipient_id = isset($_POST['private_recipient_id']) ? (int)$_POST['private_recipient_id'] : null;

if (empty($message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Message cannot be empty']);
    exit();
}

$message = sanitize_input($message);

$stmt = $pdo->prepare(
    'INSERT INTO chat_messages (user_id, username, message, is_private, private_recipient_id) 
     VALUES (?, ?, ?, ?, ?)'
);

if ($stmt->execute([$user_id, $username, $message, $is_private, $private_recipient_id])) {
    echo json_encode([
        'success' => true,
        'message_id' => $pdo->lastInsertId()
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to send message']);
}

function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
?>