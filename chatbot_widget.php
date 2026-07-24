<?php
// chatbot_widget.php
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/SMS/";
?>
<!-- Chatbot Floating Button -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<div id="chatbot-fab" class="shadow-lg" onclick="toggleChatbot()">
    <i class="bi bi-chat-dots-fill fs-3"></i>
</div>

<!-- Chatbot Window -->
<div id="chatbot-window" class="shadow-lg d-none">
    <!-- Header -->
    <div class="chatbot-header">
        <div class="d-flex align-items-center">
            <div class="chatbot-avatar me-2">
                <i class="bi bi-robot fs-5"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold">Virtual Assistant</h6>
                <small class="text-white-50">Online</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" onclick="toggleChatbot()" aria-label="Close"></button>
    </div>
    
    <!-- Chat Body -->
    <div id="chatbot-body" class="chatbot-body">
        <!-- Messages will be appended here -->
    </div>
    
    <!-- Typing Indicator (Hidden by default) -->
    <div id="chatbot-typing" class="chatbot-typing d-none">
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
    </div>

    <!-- Input Area -->
    <div class="chatbot-footer">
        <input type="text" id="chatbot-input" class="form-control" placeholder="Type a message..." onkeypress="handleChatEnter(event)">
        <button class="btn btn-primary" onclick="sendChatMessageFromInput()">
            <i class="bi bi-send-fill"></i>
        </button>
    </div>
</div>

<style>
    /* Chatbot Styles */
    #chatbot-fab {
        position: fixed;
        bottom: 100px; /* Moved up to avoid overlapping with scroll-to-top */
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        z-index: 1050;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    #chatbot-fab:hover {
        transform: scale(1.1);
        box-shadow: 0 10px 20px rgba(13, 110, 253, 0.4) !important;
    }

    #chatbot-window {
        position: fixed;
        bottom: 170px;
        right: 30px;
        width: 350px;
        height: 500px;
        background-color: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important;
        transition: all 0.3s ease-in-out;
        transform-origin: bottom right;
    }

    .chatbot-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chatbot-avatar {
        background: rgba(255, 255, 255, 0.2);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .chatbot-body {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background-color: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .chat-message {
        max-width: 85%;
        padding: 10px 15px;
        border-radius: 15px;
        font-size: 0.9rem;
        line-height: 1.4;
        animation: fadeIn 0.3s ease-in-out;
    }

    .bot-message {
        align-self: flex-start;
        background-color: #ffffff;
        color: #333;
        border-bottom-left-radius: 2px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .user-message {
        align-self: flex-end;
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: #ffffff;
        border-bottom-right-radius: 2px;
        box-shadow: 0 2px 5px rgba(13, 110, 253, 0.2);
    }
    
    .chatbot-footer {
        padding: 15px;
        background-color: #ffffff;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
    }

    .chatbot-footer input {
        border-radius: 20px;
        padding-left: 15px;
        border: 1px solid #dee2e6;
    }
    
    .chatbot-footer input:focus {
        box-shadow: none;
        border-color: #0d6efd;
    }

    .chatbot-footer button {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        min-width: 40px;
        flex-shrink: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0;
    }

    .chat-options-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 10px;
    }
    
    .chat-option-btn {
        background: #e9ecef;
        border: 1px solid #dee2e6;
        color: #0d6efd;
        border-radius: 20px;
        padding: 8px 12px;
        font-size: 0.85rem;
        text-align: left;
        cursor: pointer;
        transition: all 0.2s ease;
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .chat-option-btn:hover {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    /* Typing Animation */
    .chatbot-typing {
        padding: 10px 15px;
        background-color: #ffffff;
        align-self: flex-start;
        border-radius: 15px;
        border-bottom-left-radius: 2px;
        display: flex;
        gap: 5px;
        align-items: center;
        margin-left: 15px;
        margin-bottom: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        background-color: #0d6efd;
        border-radius: 50%;
        animation: typingBounce 1.4s infinite ease-in-out both;
    }

    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }

    @keyframes typingBounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    const chatQuestions = [
        "How to apply for a scholarship?",
        "What is the eligibility criteria?",
        "How to track my application status?",
        "What documents are required?",
        "How to contact support?"
    ];

    function toggleChatbot() {
        const window = document.getElementById('chatbot-window');
        if (window.classList.contains('d-none')) {
            // Opening chat: Clear history and initialize
            initChat();
            window.classList.remove('d-none');
        } else {
            // Closing chat
            window.classList.add('d-none');
        }
    }
    
    function initChat() {
        const chatBody = document.getElementById('chatbot-body');
        chatBody.innerHTML = '';
        
        appendMessage("Hi there! 👋 I am your Virtual Assistant. Please select a question below:", 'bot');
        showQuestions();
    }
    
    function showQuestions() {
        const chatBody = document.getElementById('chatbot-body');
        
        const optionsContainer = document.createElement('div');
        optionsContainer.className = 'chat-options-container';
        
        chatQuestions.forEach(q => {
            const btn = document.createElement('button');
            btn.className = 'chat-option-btn';
            btn.innerText = q;
            btn.onclick = () => sendChatMessage(q);
            optionsContainer.appendChild(btn);
        });
        
        chatBody.appendChild(optionsContainer);
        scrollToBottom();
    }

    function handleChatEnter(event) {
        if (event.key === 'Enter') {
            sendChatMessageFromInput();
        }
    }

    function sendChatMessageFromInput() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        if (message === '') return;
        input.value = '';
        sendChatMessage(message);
    }

    function sendChatMessage(message) {
        // Remove existing question buttons so they can't be clicked multiple times concurrently
        const optionsContainers = document.querySelectorAll('.chat-options-container');
        optionsContainers.forEach(c => c.remove());

        // 1. Add User Message to UI
        appendMessage(message, 'user');

        // 2. Show Typing Indicator
        const typingIndicator = document.getElementById('chatbot-typing');
        typingIndicator.classList.remove('d-none');
        scrollToBottom();

        // 3. Send AJAX Request to backend API
        fetch('<?php echo $base_url; ?>chatbot_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message=' + encodeURIComponent(message)
        })
        .then(response => response.json())
        .then(data => {
            // Hide typing indicator
            typingIndicator.classList.add('d-none');
            
            // Append Bot Response
            if(data.status === 'success') {
                appendMessage(data.reply, 'bot');
            } else {
                appendMessage("Sorry, I'm having trouble connecting to the server.", 'bot');
            }
            
            // Wait 5 seconds, then show questions again
            setTimeout(() => {
                const isWindowOpen = !document.getElementById('chatbot-window').classList.contains('d-none');
                if(isWindowOpen) {
                    appendMessage("Do you have any other questions?", 'bot');
                    showQuestions();
                }
            }, 5000);
        })
        .catch(error => {
            typingIndicator.classList.add('d-none');
            appendMessage("Sorry, a network error occurred.", 'bot');
            console.error('Chatbot Error:', error);
            
            setTimeout(() => {
                const isWindowOpen = !document.getElementById('chatbot-window').classList.contains('d-none');
                if(isWindowOpen) showQuestions();
            }, 5000);
        });
    }

    function appendMessage(text, sender) {
        const chatBody = document.getElementById('chatbot-body');
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message ${sender}-message`;
        msgDiv.innerHTML = text;
        
        chatBody.appendChild(msgDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        const chatBody = document.getElementById('chatbot-body');
        // Because of smooth transitions, a slight delay helps with scrolling accurately
        setTimeout(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 50);
    }
</script>
