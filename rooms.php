<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';

require_login();

$user_id = $_SESSION['user_id'];

// Get all public rooms
$rooms_stmt = $pdo->query(
    'SELECT r.*, u.username as creator_name, 
            (SELECT COUNT(*) FROM room_members WHERE room_id = r.id) as member_count
     FROM rooms r 
     JOIN users u ON r.creator_id = u.id 
     WHERE r.is_public = TRUE 
     ORDER BY r.created_at DESC'
);
$rooms = $rooms_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Rooms - Media Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="header-content">
                <div class="logo">
                    <svg class="lightning-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Public rooms</span>
                </div>
                <div class="header-actions">
                    <a href="index.php" class="btn-nav">← Home</a>
                    <button class="btn-primary" onclick="showCreateRoomModal()">Create room</button>
                </div>
            </div>
        </header>

        <main class="rooms-container">
            <?php if (empty($rooms)): ?>
                <p class="no-rooms">No public rooms yet. Create one to get started!</p>
            <?php else: ?>
                <div class="rooms-list">
                    <?php foreach ($rooms as $room): ?>
                        <div class="room-card">
                            <div class="room-header">
                                <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                                <span class="room-stats">
                                    👥 <?php echo $room['member_count']; ?> members
                                </span>
                            </div>
                            <?php if (!empty($room['description'])): ?>
                                <p class="room-description"><?php echo htmlspecialchars($room['description']); ?></p>
                            <?php endif; ?>
                            <div class="room-footer">
                                <small>Created by <?php echo htmlspecialchars($room['creator_name']); ?></small>
                                <div class="room-actions">
                                    <button class="btn-primary" onclick="joinRoom(<?php echo $room['id']; ?>)">Open</button>
                                    <button class="btn-secondary" onclick="copyRoomLink(<?php echo $room['id']; ?>)">Copy link</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Create Room Modal -->
    <div id="createRoomModal" class="modal hidden">
        <div class="modal-content">
            <h2>Create a New Room</h2>
            <form id="createRoomForm">
                <div class="form-group">
                    <label for="roomName">Room Name *</label>
                    <input type="text" id="roomName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="roomDesc">Description</label>
                    <textarea id="roomDesc" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="checkbox">
                        <input type="checkbox" id="roomPublic" name="is_public" checked>
                        <span>Make room public</span>
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Create Room</button>
                    <button type="button" class="btn-secondary" onclick="closeModal('createRoomModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showCreateRoomModal() {
            document.getElementById('createRoomModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function joinRoom(roomId) {
            window.location.href = 'room.php?id=' + roomId;
        }

        function copyRoomLink(roomId) {
            const link = window.location.origin + '/room.php?id=' + roomId;
            navigator.clipboard.writeText(link).then(() => {
                alert('Room link copied!');
            });
        }

        document.getElementById('createRoomForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            const response = await fetch('api/create_room.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            if (data.success) {
                window.location.href = 'room.php?id=' + data.room_id;
            } else {
                alert('Error: ' + data.error);
            }
        });
    </script>
</body>
</html>