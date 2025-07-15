import { GoogleGenerativeAI } from "@google/generative-ai";

const easyPiInfo =  `
EasyPi – Frequently Asked Questions (FAQ)

About EasyPi

Q: Who are the Developers of EasyPi?
A: Mark, Melchi, Red & Rizza

Q: What is EasyPi?
A: EasyPi is a task management web application designed to help simplify your daily productivity using smart, small solutions. It allows users to create, manage, and track tasks efficiently while also providing options to customize account settings.

Task Management

Q: How do I add a new task in EasyPi?
A: To add a task, go to the myAdd Task page. Enter the task title, select the priority level (Low, Medium, or High), and choose the status (To Do, In Progress, Done). Then click Add or Save Task.

Q: Can Pie-chan help me create a task?
A: Yes! 🥧 Pie-chan will guide you step-by-step with the following questions:
1. Task Title – What’s the title of your task?
2. Description – Can you give a short description?
3. Deadline – When is it due? (Use format: YYYY-MM-DDTHH:MM)
   - Note: Past dates and times are not accepted.
4. Priority – Choose one (Low, Medium, High)
5. Status – Choose one (To Do, In Progress, Done)
6. Task Image – The default image will be used: ../assets/working.png

Once all information is confirmed, Pie-chan will save the task for you! 🎉

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
    typingIndicator.remove();
    chatContainer.appendChild(typingIndicator);
    typingIndicator.style.display = 'flex';
    isTyping = true;
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
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

async function generateBotResponse(userMessage) {
    try {
        if (API_KEY === "YOUR_GEMINI_API_KEY_HERE") {
            return "Hi! I'm Pie-chan 🥧 Please configure your Gemini API key to chat with me!";
        }
        const model = genAI.getGenerativeModel({ model: "gemini-2.0-flash-exp" });
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
    async function fetchTaskMeta() {
    const res = await fetch('../includes/get_task_meta.php');
    return await res.json();
}

async function submitNewTask(taskData) {
    const res = await fetch('../includes/ai_add_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(taskData)
    });
    return await res.json();
}
if (conversationStep) {
    await handleTaskInput(message);
    return;
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