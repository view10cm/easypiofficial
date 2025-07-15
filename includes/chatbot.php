   <head>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <style>
        /* Floating Chat Button */
        .chat-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            cursor: pointer;
            z-index: 1;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(0,0,0,0.3);
        }

        .chat-toggle.hidden {
            display: none;
        }

        /* Chat Window */
        .chatwindow {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 380px;
            height: 600px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow: hidden;
            z-index: 1001;
            transform: translateY(100%) scale(0.8);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .chatwindow.active {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .chatwindow {
                width: calc(100vw - 40px);
                height: 500px;
                bottom: 10px;
                right: 20px;
                left: 20px;
            }
        }
        
        .chat-header {
            background: rgba(255,255,255,0.95);
            padding: 20px;
            text-align: center;
            position: relative;
            backdrop-filter: blur(10px);
        }
        
        .chat-header h2 {
            color: #667eea;
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .chat-header .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 1.5rem;
        }
        
        .close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #666;
            cursor: pointer;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .close:hover {
            background: rgba(255,0,0,0.1);
            color: #ff4757;
        }
        
        .chat {
            height: 400px;
            padding: 20px;
            overflow-y: auto;
            background: rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
        }
        
        .message {
            margin-bottom: 15px;
            animation: fadeIn 0.3s ease;
            display: flex;
            width: 100%;
        }
        
        .message.user-message {
            justify-content: flex-end;
        }
        
        .message.bot-message {
            justify-content: flex-start;
        }
        
        .model {
            background: rgba(255,255,255,0.9);
            padding: 12px 16px;
            border-radius: 18px 18px 18px 5px;
            color: #333;
            max-width: 80%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-size: 0.9rem;
        }
        
        .user {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 12px 16px;
            border-radius: 18px 18px 5px 18px;
            color: white;
            max-width: 80%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-size: 0.9rem;
        }
        
        .input-area {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
        }
        
        .input-group {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 25px;
            overflow: hidden;
        }
        
        .input {
            border: none;
            padding: 15px 20px;
            font-size: 0.9rem;
            background: white;
        }
        
        .input:focus {
            outline: none;
            box-shadow: none;
        }
        
        .send {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .send:hover:not(:disabled) {
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
            transform: translateY(-1px);
        }
        
        .send:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .input:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .chat::-webkit-scrollbar {
            width: 6px;
        }
        
        .chat::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        
        .chat::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }
        
        .typing-indicator {
            display: none;
            align-items: center;
            gap: 5px;
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
            padding: 10px 0;
            margin-bottom: 15px;
            width: 100%;
        }
        
        .typing-dots {
            display: flex;
            gap: 3px;
        }
        
        .typing-dots span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            animation: typing 1.4s infinite;
        }
        
        .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
        
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-10px); }
        }


    </style>
</head>
<div class="chatbot-container">
    <!-- Chat Toggle Button -->
    <button class="chat-toggle" id="chatToggle" onclick="toggleChat()">
        <i class="fas fa-comments"></i>
    </button>

    <!-- Chat Window -->
    <div class="chatwindow" id="chatWindow">
        <div class="chat-header">
            <div class="avatar">
                <i class="fas fa-robot"></i>
            </div>
            <h2>Pie-chan</h2>
            <button class="close" onclick="closeChatbot()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="chat" id="chatContainer">
            <div class="message bot-message">
                <div class="model">Hi there! I'm Pie-chan 🥧 How can I help you today?</div>
            </div>
            <div class="typing-indicator" id="typingIndicator">
                <span>Pie-chan is typing</span>
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
        
        <div class="input-area">
            <div class="input-group">
                <input type="text" class="form-control input" id="messageInput" placeholder="Type your message here..." onkeypress="handleKeyPress(event)">
                <button class="btn send" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div> 
    </div>