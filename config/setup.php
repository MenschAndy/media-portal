<?php
require_once 'database.php';

// Create tables
$sql = [
    'users' => "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        avatar VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    
    'galleries' => "CREATE TABLE IF NOT EXISTS galleries (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        name VARCHAR(100),
        description TEXT,
        is_public BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    'media' => "CREATE TABLE IF NOT EXISTS media (
        id INT PRIMARY KEY AUTO_INCREMENT,
        gallery_id INT NOT NULL,
        user_id INT NOT NULL,
        filename VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        media_type ENUM('image', 'video') NOT NULL,
        file_size INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (gallery_id) REFERENCES galleries(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    'chat_messages' => "CREATE TABLE IF NOT EXISTS chat_messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        username VARCHAR(50),
        message TEXT NOT NULL,
        is_private BOOLEAN DEFAULT FALSE,
        private_recipient_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    'rooms' => "CREATE TABLE IF NOT EXISTS rooms (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        creator_id INT NOT NULL,
        is_public BOOLEAN DEFAULT TRUE,
        password VARCHAR(255),
        max_users INT DEFAULT 100,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    'room_members' => "CREATE TABLE IF NOT EXISTS room_members (
        id INT PRIMARY KEY AUTO_INCREMENT,
        room_id INT NOT NULL,
        user_id INT NOT NULL,
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_member (room_id, user_id)
    )"
];

foreach ($sql as $table => $query) {
    try {
        $pdo->exec($query);
        echo "✓ Table '{$table}' created or already exists\n";
    } catch (PDOException $e) {
        echo "✗ Error with '{$table}': " . $e->getMessage() . "\n";
    }
}

echo "\nDatabase setup complete!\n";
?>