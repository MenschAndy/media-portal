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
$upload_dir = '../uploads/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file provided']);
    exit();
}

$file = $_FILES['file'];
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm'];

if (!in_array($file['type'], $allowed_types)) {
    http_response_code(400);
    echo json_encode(['error' => 'File type not allowed']);
    exit();
}

if ($file['size'] > 100 * 1024 * 1024) { // 100MB limit
    http_response_code(400);
    echo json_encode(['error' => 'File too large']);
    exit();
}

$filename = uniqid() . '_' . basename($file['name']);
$file_path = $upload_dir . $filename;

if (move_uploaded_file($file['tmp_name'], $file_path)) {
    // Get or create default gallery
    $gallery_stmt = $pdo->prepare('SELECT id FROM galleries WHERE user_id = ? LIMIT 1');
    $gallery_stmt->execute([$user_id]);
    $gallery = $gallery_stmt->fetch();
    
    if (!$gallery) {
        $insert_gallery = $pdo->prepare('INSERT INTO galleries (user_id, name) VALUES (?, ?)');
        $insert_gallery->execute([$user_id, 'Default Gallery']);
        $gallery_id = $pdo->lastInsertId();
    } else {
        $gallery_id = $gallery['id'];
    }
    
    // Determine media type
    $media_type = strpos($file['type'], 'image') !== false ? 'image' : 'video';
    
    // Insert media record
    $insert_media = $pdo->prepare(
        'INSERT INTO media (gallery_id, user_id, filename, file_path, media_type, file_size) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $insert_media->execute([
        $gallery_id,
        $user_id,
        $filename,
        $file_path,
        $media_type,
        $file['size']
    ]);
    
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'path' => $file_path,
        'type' => $media_type
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Upload failed']);
}
?>