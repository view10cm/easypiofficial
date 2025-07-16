import { GoogleGenerativeAI } from "@google/generative-ai";

const easyPiInfo = `
EasyPi – Frequently Asked Questions (FAQ)

About EasyPi

PAGES
DashBoard - overview of the task, you can see their the todo list for today, task status, and completed task
Vital Task - it is where if you have a pending task today
MyTask - it is where you can add your task
Task Categories - In this page the user can see different task status and change the name of the task priority
Settings - It is where your account is located. The user can change the username, email and password
Help -It is where the FAQ is locatated and our contact email

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

// Task creation conversation state
let conversationStep = null;
let taskData = {};

const taskCreationSteps = {
  TITLE: "title",
  DESCRIPTION: "description",
  DEADLINE: "deadline",
  PRIORITY: "priority",
  STATUS: "status",
  CONFIRM: "confirm",
};

window.toggleChat = function () {
  const chatWindow = document.getElementById("chatWindow");
  const chatToggle = document.getElementById("chatToggle");
  if (chatOpen) {
    closeChatbot();
  } else {
    openChatbot();
  }
};

function openChatbot() {
  const chatWindow = document.getElementById("chatWindow");
  const chatToggle = document.getElementById("chatToggle");
  chatWindow.classList.add("active");
  chatToggle.classList.add("hidden");
  chatOpen = true;
  setTimeout(() => {
    document.getElementById("messageInput").focus();
  }, 300);
}

window.closeChatbot = function () {
  const chatWindow = document.getElementById("chatWindow");
  const chatToggle = document.getElementById("chatToggle");
  chatWindow.classList.remove("active");
  chatToggle.classList.remove("hidden");
  chatOpen = false;
};

window.handleKeyPress = function (event) {
  if (event.key === "Enter" && !isTyping) {
    sendMessage();
  }
};

window.sendMessage = async function () {
  const messageInput = document.getElementById("messageInput");
  const chatContainer = document.getElementById("chatContainer");
  const sendButton = document.querySelector(".send");
  const message = messageInput.value.trim();
  if (message === "" || isTyping) return;
  // Add user message
  const userMessage = document.createElement("div");
  userMessage.className = "message user-message";
  userMessage.innerHTML = `<div class="user">${message}</div>`;
  chatContainer.appendChild(userMessage);
  // Clear input and disable it
  messageInput.value = "";
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
};

function showTypingIndicator() {
  const typingIndicator = document.getElementById("typingIndicator");
  const chatContainer = document.getElementById("chatContainer");
  typingIndicator.remove();
  chatContainer.appendChild(typingIndicator);
  typingIndicator.style.display = "flex";
  isTyping = true;
  chatContainer.scrollTop = chatContainer.scrollHeight;
}

function hideTypingIndicator() {
  const typingIndicator = document.getElementById("typingIndicator");
  typingIndicator.style.display = "none";
  isTyping = false;
}

function addBotResponse(response) {
  const chatContainer = document.getElementById("chatContainer");
  const botMessage = document.createElement("div");
  botMessage.className = "message bot-message";
  botMessage.innerHTML = `<div class="model">${response}</div>`;
  chatContainer.appendChild(botMessage);
  chatContainer.scrollTop = chatContainer.scrollHeight;
}

async function generateBotResponse(userMessage) {
  try {
    if (API_KEY === "YOUR_GEMINI_API_KEY_HERE") {
      return "Hi! I'm Pie-chan 🥧 Please configure your Gemini API key to chat with me!";
    }

    // Check if user wants to create a task
    if (conversationStep === null && isTaskCreationRequest(userMessage)) {
      return startTaskCreation();
    }

    // Handle task creation conversation
    if (conversationStep !== null) {
      return await handleTaskCreationStep(userMessage);
    }

    // Normal chat response
    const model = genAI.getGenerativeModel({ model: "gemini-2.0-flash-exp" });
    const prompt = `${easyPiInfo}

You are Pie-chan, a cute and friendly AI assistant for EasyPi. You love using emojis, especially 🥧. You're helpful, cheerful, and speak in a warm, caring tone. Keep responses concise but engaging. Always try to be positive and supportive. 

IMPORTANT: If the user asks about creating tasks, adding tasks, or task management, encourage them to use specific phrases like "create a task" or "add a task" to start the step-by-step guided process. Tell them that you can guide them through each step one by one.

Use the EasyPi information provided above to answer questions about the application.

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

function isTaskCreationRequest(message) {
  const taskKeywords = [
    "create task",
    "add task",
    "new task",
    "make task",
    "create a task",
    "add a task",
    "make a task",
    "help me create",
    "help me add",
    "help me make",
    "task creation",
    "task help",
    "add new task",
    "create new task",
    "make new task",
    "start task",
    "begin task",
    "task guide",
    "task wizard",
    "i want to create",
    "i want to add",
    "i need to create",
    "i need to add",
    "can you help me create",
    "can you help me add",
    "how to create",
    "how to add",
    "guide me through",
    "walk me through",
    "step by step",
  ];

  const lowerMessage = message.toLowerCase();
  return taskKeywords.some((keyword) => lowerMessage.includes(keyword));
}

function startTaskCreation() {
  conversationStep = taskCreationSteps.TITLE;
  taskData = {};

  return `Hi there! 🥧 I'd love to help you create a task! Let's go through this step by step.

**Step 1 of 5: Task Title**
What would you like to name your task? 

💡 *Tips:*
- Keep it clear and specific
- Example: "Complete project proposal" or "Buy groceries"
- Avoid vague titles like "Do stuff"

Just type your task title and I'll guide you to the next step! ✨
*Type "cancel" anytime to stop the process.*`;
}

async function handleTaskCreationStep(userMessage) {
  // Check if user wants to cancel at any step
  if (
    userMessage.toLowerCase().trim() === "cancel" ||
    userMessage.toLowerCase().trim() === "stop" ||
    userMessage.toLowerCase().trim() === "quit"
  ) {
    resetTaskCreation();
    return `No problem! 🥧 Task creation cancelled. 

Feel free to ask me to create a task again whenever you're ready! Is there anything else I can help you with? ✨`;
  }

  switch (conversationStep) {
    case taskCreationSteps.TITLE:
      return handleTitleStep(userMessage);

    case taskCreationSteps.DESCRIPTION:
      return handleDescriptionStep(userMessage);

    case taskCreationSteps.DEADLINE:
      return handleDeadlineStep(userMessage);

    case taskCreationSteps.PRIORITY:
      return handlePriorityStep(userMessage);

    case taskCreationSteps.STATUS:
      return handleStatusStep(userMessage);

    case taskCreationSteps.CONFIRM:
      return await handleConfirmStep(userMessage);

    default:
      resetTaskCreation();
      return "Something went wrong! Let's start over. Just ask me to create a task again! 🥧";
  }
}

function handleTitleStep(userMessage) {
  const title = userMessage.trim();

  if (title.length < 3) {
    return `Oops! 🥧 Your task title seems a bit short. Could you give me a more descriptive title? 

Please enter at least 3 characters for your task title.`;
  }

  if (title.length > 255) {
    return `Whoa! 🥧 That's quite a long title! Could you make it shorter? 

Please keep your task title under 255 characters.`;
  }

  taskData.title = title;
  conversationStep = taskCreationSteps.DESCRIPTION;

  return `Great! ✨ Your task title is: **"${title}"**

**Step 2 of 5: Description**
Now, can you give me a brief description of what this task involves?

💡 *Tips:*
- Describe what needs to be done
- Include any important details
- You can type "skip" if you don't want to add a description

Go ahead and describe your task! 🥧`;
}

function handleDescriptionStep(userMessage) {
  const description = userMessage.trim();

  if (
    description.toLowerCase() === "skip" ||
    description.toLowerCase() === "none" ||
    description.toLowerCase() === "no"
  ) {
    taskData.description = "";
  } else {
    taskData.description = description;
  }

  conversationStep = taskCreationSteps.DEADLINE;

  return `Perfect! 🥧 ${
    taskData.description
      ? `Your description: **"${taskData.description}"**`
      : "No description added."
  }

**Step 3 of 5: Deadline**
When do you need to complete this task?

💡 *Please use this format:* **YYYY-MM-DD HH:MM**
- Example: **2025-07-20 14:30** (for July 20, 2025 at 2:30 PM)
- Example: **2025-07-25 09:00** (for July 25, 2025 at 9:00 AM)

⚠️ *Note:* Past dates and times are not accepted.
*Type "cancel" to stop the process.*

Enter your deadline! ⏰`;
}

function handleDeadlineStep(userMessage) {
  const deadline = userMessage.trim();

  // Validate datetime format
  const dateTimeRegex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/;
  if (!dateTimeRegex.test(deadline)) {
    return `Oops! 🥧 That doesn't look like the right format. 

Please use: **YYYY-MM-DD HH:MM**
- Example: **2025-07-20 14:30**
- Example: **2025-07-25 09:00**

*Type "cancel" to stop the process.*
Try again! ⏰`;
  }

  // Check if the date is valid
  const inputDate = new Date(deadline.replace(" ", "T"));
  if (isNaN(inputDate.getTime())) {
    return `Hmm! 🥧 That doesn't seem like a valid date. 

Please use: **YYYY-MM-DD HH:MM**
- Make sure the month is 01-12
- Make sure the day is valid for that month
- Make sure the time is in 24-hour format (00:00 to 23:59)

*Type "cancel" to stop the process.*
Try again! ⏰`;
  }

  // Check if the date is not in the past
  const now = new Date();
  if (inputDate <= now) {
    return `Hold on! 🥧 That date and time is in the past! 

Please choose a future date and time.
Current date and time: **${now.toLocaleString()}**

*Type "cancel" to stop the process.*
Enter a deadline that's in the future! ⏰`;
  }

  taskData.deadline = deadline;
  conversationStep = taskCreationSteps.PRIORITY;

  return `Excellent! 🥧 Your deadline is: **${deadline}**

**Step 4 of 5: Priority**
How important is this task?

Please choose one of these options:
🔴 **High** - Urgent and important
🟡 **Medium** - Important but not urgent
🟢 **Low** - Nice to have, not urgent

*Type "cancel" to stop the process.*
Just type **High**, **Medium**, or **Low** ⚡`;
}

function handlePriorityStep(userMessage) {
  const priority = userMessage.trim().toLowerCase();

  const validPriorities = ["high", "medium", "low"];
  if (!validPriorities.includes(priority)) {
    return `Hmm! 🥧 I didn't understand that priority level.

Please choose one of these:
🔴 **High** - Urgent and important
🟡 **Medium** - Important but not urgent  
🟢 **Low** - Nice to have, not urgent

Just type **High**, **Medium**, or **Low** ⚡`;
  }

  taskData.priority = priority.charAt(0).toUpperCase() + priority.slice(1);
  conversationStep = taskCreationSteps.STATUS;

  const priorityEmoji =
    priority === "high" ? "🔴" : priority === "medium" ? "🟡" : "🟢";

  return `Perfect! ${priorityEmoji} Your priority is: **${taskData.priority}**

**Step 5 of 5: Status**
What's the current status of this task?

Please choose one:
📋 **To Do** - Haven't started yet
⚙️ **In Progress** - Currently working on it
✅ **Done** - Already completed

Just type **To Do**, **In Progress**, or **Done** 📝`;
}

function handleStatusStep(userMessage) {
  const status = userMessage.trim().toLowerCase();

  const statusMap = {
    "to do": "To Do",
    todo: "To Do",
    "not started": "To Do",
    "in progress": "In Progress",
    inprogress: "In Progress",
    progress: "In Progress",
    working: "In Progress",
    done: "Done",
    completed: "Done",
    finished: "Done",
  };

  const mappedStatus = statusMap[status];
  if (!mappedStatus) {
    return `Oops! 🥧 I didn't understand that status.

Please choose one of these:
📋 **To Do** - Haven't started yet
⚙️ **In Progress** - Currently working on it
✅ **Done** - Already completed

Just type **To Do**, **In Progress**, or **Done** 📝`;
  }

  taskData.status = mappedStatus;
  conversationStep = taskCreationSteps.CONFIRM;

  const statusEmoji =
    mappedStatus === "To Do"
      ? "📋"
      : mappedStatus === "In Progress"
      ? "⚙️"
      : "✅";

  return `Great! ${statusEmoji} Your status is: **${taskData.status}**

**📋 Task Summary:**
✨ **Title:** ${taskData.title}
📝 **Description:** ${taskData.description || "None"}
⏰ **Deadline:** ${taskData.deadline}
⚡ **Priority:** ${taskData.priority}
📊 **Status:** ${taskData.status}
🖼️ **Image:** Default image (../assets/working.png)

Everything looks good? 🥧
- Type **yes** or **confirm** to create the task
- Type **no** or **cancel** to start over
- Type **edit** to modify something`;
}

async function handleConfirmStep(userMessage) {
  const response = userMessage.trim().toLowerCase();

  if (
    response === "yes" ||
    response === "confirm" ||
    response === "y" ||
    response === "ok"
  ) {
    // Map priority and status to IDs
    const priorityMap = { High: 3, Medium: 2, Low: 1 };
    const statusMap = { "To Do": 1, "In Progress": 2, Done: 3 };

    // Prepare data for the endpoint
    const taskDataForAPI = {
      title: taskData.title,
      description: taskData.description,
      deadline: taskData.deadline.replace(" ", "T"), // Convert to ISO format
      priority_id: priorityMap[taskData.priority],
      status_id: statusMap[taskData.status],
      task_img: "../assets/working.png",
    };

    // Create the task
    const result = await submitNewTask(taskDataForAPI);
    const taskTitle = taskData.title; // Store before reset
    resetTaskCreation();

    if (result === "success") {
      return `🎉 Woohoo! Your task has been created successfully! 

Your task **"${taskTitle}"** is now in your task list. You can view it in the MyTask page or on your Dashboard.

Is there anything else I can help you with? 🥧✨`;
    } else {
      return `Oh no! 🥧 Something went wrong while creating your task. 

Don't worry, you can try again! Just ask me to create a task and we'll go through the steps once more. 💪`;
    }
  } else if (response === "no" || response === "cancel" || response === "n") {
    resetTaskCreation();
    return `No problem! 🥧 Task creation cancelled. 

Feel free to ask me to create a task again whenever you're ready! Is there anything else I can help you with? ✨`;
  } else if (
    response === "edit" ||
    response === "modify" ||
    response === "change"
  ) {
    resetTaskCreation();
    return `Sure! 🥧 Let's start over so you can make changes.

Just ask me to create a task and we'll go through all the steps again! Ready when you are! ✨`;
  } else {
    return `I'm not sure what you mean! 🥧 

Please respond with:
- **yes** or **confirm** to create the task
- **no** or **cancel** to start over  
- **edit** to modify something

What would you like to do? ✨`;
  }
}

function resetTaskCreation() {
  conversationStep = null;
  taskData = {};
}
async function fetchTaskMeta() {
  const res = await fetch("../includes/get_task_meta.php");
  return await res.json();
}

async function submitNewTask(taskData) {
  try {
    const response = await fetch("../includes/ai_add_task.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(taskData),
    });

    const result = await response.text();
    return result;
  } catch (error) {
    console.error("Error submitting task:", error);
    return "error";
  }
}

// Close chat when clicking outside
document.addEventListener("click", function (event) {
  const chatWindow = document.getElementById("chatWindow");
  const chatToggle = document.getElementById("chatToggle");
  if (
    chatOpen &&
    !chatWindow.contains(event.target) &&
    !chatToggle.contains(event.target)
  ) {
    closeChatbot();
  }
});
