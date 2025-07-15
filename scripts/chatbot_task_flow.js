let conversationStep = null;
let taskData = {
  title: '',
  description: '',
  deadline: '',
  priority_id: '',
  status_id: '',
  task_img: '../assets/working.png'
};

// Start the Q&A task creation
window.startAddTaskFlow = async function () {
  conversationStep = 'title';
  taskData = {
    title: '',
    description: '',
    deadline: '',
    priority_id: '',
    status_id: '',
    task_img: '../assets/working.png'
  };
  addBotResponse("Great! Let's add a new task 🥧 What's the title of your task?");
}

// Handle Q&A flow
window.handleTaskInput = async function (input) {
  const msg = input.trim();

  if (!msg) return;

  switch (conversationStep) {
    case 'title':
      taskData.title = msg;
      conversationStep = 'description';
      addBotResponse("Nice! Now, what's the description?");
      break;

    case 'description':
      taskData.description = msg;
      conversationStep = 'deadline';
      addBotResponse("When is the deadline? (e.g., 2025-08-01T15:00)");
      break;

    case 'deadline':
      const deadline = new Date(msg);
      const now = new Date();

      if (isNaN(deadline) || deadline <= now) {
        addBotResponse("Oops! Please provide a future date and time (e.g., 2025-08-01T15:00).");
        return;
      }

      taskData.deadline = msg;
      conversationStep = 'priority';

      const priorityOptions = await fetch('../includes/get_task_meta.php')
        .then(res => res.json())
        .then(data => data.priorities || []);

      let priorityMsg = "What's the priority level?\n";
      priorityOptions.forEach(p => {
        priorityMsg += `- ${p.priority_id}: ${p.priority_name}\n`;
      });

      addBotResponse(priorityMsg.trim());
      break;

    case 'priority':
      taskData.priority_id = msg;
      conversationStep = 'status';

      const statusOptions = await fetch('../includes/get_task_meta.php')
        .then(res => res.json())
        .then(data => data.statuses || []);

      let statusMsg = "Got it 🥧 Now choose the task status:\n";
      statusOptions.forEach(s => {
        statusMsg += `- ${s.status_id}: ${s.status_name}\n`;
      });

      addBotResponse(statusMsg.trim());
      break;

    case 'status':
      taskData.status_id = msg;
      conversationStep = null;

      // Submit task
      const result = await fetch('../includes/add_task.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(taskData)
      });

      const resText = await result.text();

      if (resText.includes("success")) {
        addBotResponse("Yay! 🎉 Task added successfully 🥧");
      } else {
        addBotResponse("Hmm, something went wrong while adding your task 😢 Try again later.");
      }
      break;

    default:
      generateBotResponse(msg); // fallback to FAQ logic
  }
}
