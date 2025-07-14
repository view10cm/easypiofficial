<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pie-chan Chatbot</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

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
<body>
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

    <!-- Bootstrap JS -->
    <!-- Gemini API -->
    <script type="importmap">
        {
            "imports": {
                "@google/generative-ai": "https://esm.run/@google/generative-ai"
            }
        }
    </script>
    
    <script type="module">
        import { GoogleGenerativeAI } from "@google/generative-ai";

        const easyPiInfo =  `
EasyPi – Frequently Asked Questions (FAQ)

About EasyPi

Q: Who are the Developers of EasyPi?
A: Mark, Melchi, BossA & Rizza

Q: What is EasyPi?
A: EasyPi is a task management web application designed to help simplify your daily productivity using smart, small solutions. It allows users to create, manage, and track tasks efficiently while also providing options to customize account settings.

Task Management

Q: How do I add a new task in EasyPi?
A: To add a task, go to the myAdd Task page. Enter the task title, select the priority level (Low, Medium, or High), and choose the status (To Do, In Progress, Done). Then click Add or Save Task.

Q: What are the available task priorities?
A: EasyPi lets you set a task’s priority to Low, Medium, or High based on how important or urgent the task is.

Q: What task statuses can I use?
A: Tasks in EasyPi can have the following statuses:
- To Do – The task has not been started
- In Progress – The task is currently being worked on
- Done – The task has been completed

Q: Where can I view my tasks?
A: You can view all your tasks on the Dashboard or in the Task List. From there, you can review, edit, or delete them as needed.

Q: Can I edit or delete a task?
A: Yes. Simply click on any task in your list to update its details or delete it if it is no longer needed.

Account Settings

Q: How do I change my account name?
A: Click on your profile icon and go to Settings. Under the Profile section, enter your new name and save the changes.

Q: How can I update my profile picture?
A: In the Settings section, click on your current profile picture to upload a new one.

Q: How do I change my password?
A: Go to Settings, navigate to the Password section, and enter your current password. Then, provide and confirm your new password to save the changes.

Tips and Guidance

Q: What are some tips for staying organized in EasyPi?
A: Here are some helpful suggestions:
- Use High Priority for urgent or time-sensitive tasks.
- Update the status of tasks regularly to track progress.
- Create clear and specific titles, especially for similar or repeating tasks.
- Delete completed tasks to keep your workspace clutter-free.

EasyPi FAQ and Chat Assistant – Tone of Voice Instructions

1. Friendly, but professional
Be helpful and clear without sounding too casual or too formal.
Example:
Correct: "To add a task, just go to the 'myAdd Task' page and fill in the details."
Incorrect: "Yo! Just toss your task in the box and you're good!"

2. Clear and concise
Use simple, everyday language. Avoid technical jargon unless necessary.
Break information into short, understandable steps.

3. Encouraging and supportive
Build user confidence by using positive, reassuring language.
Example:
Correct: "You’re doing great! Updating your profile is simple—just follow these steps."

4. Solution-focused
Always guide the user toward the next step or resolution.
If something is unclear or fails, offer a helpful workaround or suggestion.

5. Consistent use of voice and terms
Use the same terms throughout the interface and documentation.
Refer to "myAdd Task" consistently when talking about task creation.
Avoid switching between formal and casual tone within the same section.
`;

        // You'll need to set your API key here
        const API_KEY = "AIzaSyAXAsVk4FyUKgJPfw-CFs199M0e4NWnNRQ"; // Replace with your actual API key
        const genAI = new GoogleGenerativeAI(API_KEY);

        let chatOpen = false;
        let isTyping = false;

        window.toggleChat = function() {
            const chatWindow = document.getElementById('chatWindow');
            const chatToggle = document.getElementById('chatToggle');
            
            if (chatOpen) {
                closeChatbot();
            } else {
                openChatbot();
            }
        }

        function openChatbot() {
            const chatWindow = document.getElementById('chatWindow');
            const chatToggle = document.getElementById('chatToggle');
            
            chatWindow.classList.add('active');
            chatToggle.classList.add('hidden');
            chatOpen = true;
            
            // Focus on input
            setTimeout(() => {
                document.getElementById('messageInput').focus();
            }, 300);
        }

        window.closeChatbot = function() {
            const chatWindow = document.getElementById('chatWindow');
            const chatToggle = document.getElementById('chatToggle');
            
            chatWindow.classList.remove('active');
            chatToggle.classList.remove('hidden');
            chatOpen = false;
        }

        window.handleKeyPress = function(event) {
            if (event.key === 'Enter' && !isTyping) {
                sendMessage();
            }
        }

        window.sendMessage = async function() {
            const messageInput = document.getElementById('messageInput');
            const chatContainer = document.getElementById('chatContainer');
            const sendButton = document.querySelector('.send');
            const message = messageInput.value.trim();
            
            if (message === '' || isTyping) return;
            
            // Add user message
            const userMessage = document.createElement('div');
            userMessage.className = 'message user-message';
            userMessage.innerHTML = `<div class="user">${message}</div>`;
            chatContainer.appendChild(userMessage);
            
            // Clear input and disable it
            messageInput.value = '';
            messageInput.disabled = true;
            sendButton.disabled = true;
            
            // Show typing indicator right after user message
            showTypingIndicator();
            
            // Scroll to bottom
            chatContainer.scrollTop = chatContainer.scrollHeight;
            
            // Get AI response
            const response = await generateBotResponse(message);
            hideTypingIndicator();
            addBotResponse(response);
            
            // Re-enable input
            messageInput.disabled = false;
            sendButton.disabled = false;
            messageInput.focus();
        }

        function showTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            const chatContainer = document.getElementById('chatContainer');
            
            // Remove typing indicator from its current position
            typingIndicator.remove();
            
            // Add it after the last message
            chatContainer.appendChild(typingIndicator);
            
            typingIndicator.style.display = 'flex';
            isTyping = true;
            
            // Scroll to bottom to show typing indicator
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function hideTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            typingIndicator.style.display = 'none';
            isTyping = false;
        }

        function addBotResponse(response) {
            const chatContainer = document.getElementById('chatContainer');
            
            const botMessage = document.createElement('div');
            botMessage.className = 'message bot-message';
            botMessage.innerHTML = `<div class="model">${response}</div>`;
            chatContainer.appendChild(botMessage);
            
            // Scroll to bottom
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        async function generateBotResponse(userMessage) {
            try {
                if (API_KEY === "YOUR_GEMINI_API_KEY_HERE") {
                    // Fallback when API key is not configured
                    return "Hi! I'm Pie-chan 🥧 Please configure your Gemini API key to chat with me!";
                }

                const model = genAI.getGenerativeModel({ model: "gemini-2.0-flash-exp" });
                
                // Use easyPiInfo as system instructions for Pie-chan
                const prompt = `${easyPiInfo}

You are Pie-chan, a cute and friendly AI assistant for EasyPi. You love using emojis, especially 🥧. You're helpful, cheerful, and speak in a warm, caring tone. Keep responses concise but engaging. Always try to be positive and supportive. Use the EasyPi information provided above to answer questions about the application.

User: ${userMessage}

Pie-chan:`;

                const result = await model.generateContent(prompt);
                const response = await result.response;
                return response.text();
            } catch (error) {
                console.error("Error generating bot response:", error);
                return "Sorry, I'm having trouble connecting right now. But I'm still here for you! 🥧";
            }
        }

        // Close chat when clicking outside
        document.addEventListener('click', function(event) {
            const chatWindow = document.getElementById('chatWindow');
            const chatToggle = document.getElementById('chatToggle');
            
            if (chatOpen && !chatWindow.contains(event.target) && !chatToggle.contains(event.target)) {
                closeChatbot();
            }
        });
    </script>
</body>
</html>