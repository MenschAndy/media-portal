<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user info and online status
$user_stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();

// Get online users count
$online_stmt = $pdo->query('SELECT COUNT(*) as count FROM users WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)');
$online_count = $online_stmt->fetch()['count'];

// Update user last activity
$update_stmt = $pdo->prepare('UPDATE users SET last_activity = NOW() WHERE id = ?');
$update_stmt->execute([$user_id]);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Portal - Teens</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <div class="logo">
                    <svg class="lightning-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Teens</span>
                </div>
                <div class="header-stats">
                    <span>Online: <strong><?php echo $online_count; ?></strong></span>
                    <span>Expires in: <strong>40m 45s</strong></span>
                    <button class="btn-report">Report</button>
                </div>
            </div>
        </header>

        <!-- Navigation Tabs -->
        <nav class="nav-tabs">
            <a href="#media" class="tab-btn active" data-tab="media">
                <span class="icon">📱</span>
                Media
            </a>
            <a href="#chat" class="tab-btn" data-tab="chat">
                <span class="icon">💬</span>
                Chat
            </a>
        </nav>

        <div class="main-content">
            <!-- Media Tab -->
            <section id="media" class="tab-content active">
                <div class="gallery-container">
                    <h2>Gallery</h2>
                    <div class="gallery-grid" id="gallery">
                        <!-- Images loaded via JavaScript -->
                    </div>
                    <div class="upload-section">
                        <button class="btn-upload" id="uploadBtn">Upload</button>
                        <p>Drag & drop anywhere in this panel</p>
                    </div>
                </div>
            </section>

            <!-- Chat Tab -->
            <section id="chat" class="tab-content">
                <h2>Chat</h2>
                <div class="chat-messages" id="chatMessages">
                    <!-- Messages loaded via JavaScript -->
                </div>
                <div class="chat-input-section">
                    <textarea id="messageInput" placeholder="Type a message..." rows="3"></textarea>
                    <div class="chat-footer">
                        <label class="checkbox">
                            <input type="checkbox" id="privateReply">
                            <span>Private reply</span>
                        </label>
                        <button class="btn-send" id="sendBtn">Send</button>
                    </div>
                </div>
            </section>
        </div>

        <!-- Sidebar Chat -->
        <aside class="sidebar-chat">
            <h3>Chat</h3>
            <div class="sidebar-messages" id="sidebarMessages">
                <!-- Messages loaded via JavaScript -->
            </div>
        </aside>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="modal hidden">
        <div class="modal-content">
            <h2>Upload Media</h2>
            <div class="upload-dropzone" id="dropzone">
                <p>Drag files here or click to select</p>
                <input type="file" id="fileInput" multiple accept="image/*,video/*" hidden>
            </div>
            <div id="uploadProgress"></div>
            <button class="btn-close" onclick="document.getElementById('uploadModal').classList.add('hidden')">Close</button>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>