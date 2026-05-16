// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const tabName = btn.dataset.tab;
        
        // Remove active class from all buttons and contents
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked button and corresponding content
        btn.classList.add('active');
        document.getElementById(tabName).classList.add('active');
    });
});

// Set first tab as active
window.addEventListener('load', () => {
    document.querySelector('.tab-btn').classList.add('active');
    document.querySelector('.tab-content').classList.add('active');
    
    loadMedia();
    loadMessages();
    
    // Auto-refresh messages every 2 seconds
    setInterval(loadMessages, 2000);
});

// Load gallery media
async function loadMedia() {
    try {
        const response = await fetch('api/get_media.php');
        const data = await response.json();
        
        if (data.success) {
            const gallery = document.getElementById('gallery');
            gallery.innerHTML = '';
            
            data.data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'gallery-item';
                
                if (item.media_type === 'image') {
                    div.innerHTML = `<img src="${item.file_path}" alt="${item.filename}" loading="lazy">`;
                } else {
                    div.innerHTML = `<video><source src="${item.file_path}"></video>`;
                }
                
                div.addEventListener('click', () => openMediaModal(item));
                gallery.appendChild(div);
            });
        }
    } catch (error) {
        console.error('Failed to load media:', error);
    }
}

// Load chat messages
async function loadMessages() {
    try {
        const response = await fetch('api/get_messages.php');
        const data = await response.json();
        
        if (data.success) {
            // Update main chat
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.innerHTML = '';
            
            data.data.forEach(msg => {
                const div = document.createElement('div');
                div.className = 'chat-message' + (msg.is_private ? ' private' : '');
                
                const date = new Date(msg.created_at);
                const time = date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
                
                div.innerHTML = `
                    <div class="chat-message-header">
                        <span class="chat-message-author">${escapeHtml(msg.username)}</span>
                        <span class="chat-message-time">${time}</span>
                    </div>
                    <div class="chat-message-text">${escapeHtml(msg.message)}</div>
                `;
                
                chatMessages.appendChild(div);
            });
            
            // Update sidebar chat
            const sidebarMessages = document.getElementById('sidebarMessages');
            sidebarMessages.innerHTML = '';
            
            data.data.slice(-5).forEach(msg => {
                const div = document.createElement('div');
                div.className = 'sidebar-message';
                
                div.innerHTML = `
                    <div class="sidebar-message-author">${escapeHtml(msg.username)}</div>
                    <div class="sidebar-message-text">${escapeHtml(msg.message)}</div>
                `;
                
                sidebarMessages.appendChild(div);
            });
            
            // Auto-scroll to latest message
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    } catch (error) {
        console.error('Failed to load messages:', error);
    }
}

// Send message
document.getElementById('sendBtn').addEventListener('click', sendMessage);
document.getElementById('messageInput').addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && e.ctrlKey) {
        sendMessage();
    }
});

async function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    const isPrivate = document.getElementById('privateReply').checked;
    
    if (!message) return;
    
    const formData = new FormData();
    formData.append('message', message);
    formData.append('is_private', isPrivate);
    
    try {
        const response = await fetch('api/send_message.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            input.value = '';
            loadMessages();
        } else {
            alert('Error: ' + data.error);
        }
    } catch (error) {
        console.error('Failed to send message:', error);
    }
}

// Upload functionality
document.getElementById('uploadBtn').addEventListener('click', () => {
    document.getElementById('uploadModal').classList.remove('hidden');
});

const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('fileInput');

dropzone.addEventListener('click', () => fileInput.click());

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => {
        dropzone.style.background = 'rgba(100, 150, 255, 0.2)';
    });
});

['dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => {
        dropzone.style.background = 'rgba(100, 150, 255, 0.05)';
    });
});

dropzone.addEventListener('drop', (e) => {
    const files = e.dataTransfer.files;
    handleFiles(files);
});

fileInput.addEventListener('change', (e) => {
    handleFiles(e.target.files);
});

async function handleFiles(files) {
    const progressDiv = document.getElementById('uploadProgress');
    progressDiv.innerHTML = '';
    
    for (let file of files) {
        const formData = new FormData();
        formData.append('file', file);
        
        try {
            const response = await fetch('api/upload.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                const p = document.createElement('p');
                p.style.color = '#6bff6b';
                p.textContent = '✓ ' + file.name + ' uploaded';
                progressDiv.appendChild(p);
            } else {
                const p = document.createElement('p');
                p.style.color = '#ff6b6b';
                p.textContent = '✗ ' + file.name + ': ' + data.error;
                progressDiv.appendChild(p);
            }
        } catch (error) {
            console.error('Upload error:', error);
        }
    }
    
    loadMedia();
}

function openMediaModal(item) {
    // You can expand this to show full media view
    console.log('Opening:', item);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}