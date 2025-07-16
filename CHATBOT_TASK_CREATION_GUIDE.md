# Enhanced Chatbot Task Creation Guide

## Overview

The chatbot (Pie-chan) now supports a **step-by-step guided task creation process** that walks users through creating tasks with detailed guidance and validation.

## How It Works

### 1. Starting Task Creation

Users can trigger the task creation flow by saying any of these phrases:

- "create a task"
- "add a task"
- "new task"
- "make a task"
- "help me create a task"
- "task creation"
- "i want to create a task"
- "can you help me add a task"
- "guide me through creating a task"

### 2. Step-by-Step Process

#### Step 1: Task Title

- **Prompt**: "What would you like to name your task?"
- **Validation**: Must be 3-255 characters
- **Tips**: Keep it clear and specific
- **Examples**: "Complete project proposal", "Buy groceries"

#### Step 2: Task Description

- **Prompt**: "Can you give me a brief description?"
- **Validation**: Optional - can type "skip", "none", or "no"
- **Tips**: Describe what needs to be done

#### Step 3: Deadline

- **Prompt**: "When do you need to complete this task?"
- **Format**: YYYY-MM-DD HH:MM (e.g., 2025-07-20 14:30)
- **Validation**:
  - Must match exact format
  - Must be a valid date
  - Cannot be in the past
- **Examples**: "2025-07-20 14:30", "2025-12-25 09:00"

#### Step 4: Priority

- **Prompt**: "How important is this task?"
- **Options**:
  - 🔴 **High** - Urgent and important
  - 🟡 **Medium** - Important but not urgent
  - 🟢 **Low** - Nice to have, not urgent
- **Validation**: Must be exactly "High", "Medium", or "Low"

#### Step 5: Status

- **Prompt**: "What's the current status?"
- **Options**:
  - 📋 **To Do** - Haven't started yet
  - ⚙️ **In Progress** - Currently working on it
  - ✅ **Done** - Already completed
- **Validation**: Accepts "To Do", "In Progress", "Done" and common variations

#### Step 6: Confirmation

- **Prompt**: Shows complete task summary
- **Options**:
  - Type "yes" or "confirm" to create
  - Type "no" or "cancel" to abort
  - Type "edit" to start over

### 3. Advanced Features

#### Cancel Anytime

- Users can type "cancel", "stop", or "quit" at any step to exit
- The process will be reset and users can start over

#### Smart Validation

- **Date Validation**: Checks format, validity, and ensures future dates
- **Length Validation**: Ensures appropriate title length
- **Flexible Input**: Accepts various forms of status/priority (e.g., "todo", "in progress", "high priority")

#### Error Handling

- Clear error messages with examples
- Helpful tips for correct input
- Graceful handling of invalid responses

### 4. User Experience Features

#### Visual Elements

- **Emojis**: 🥧 for personality, step icons for clarity
- **Progress Indicators**: "Step X of 5" format
- **Formatting**: Bold text for important information
- **Tips**: 💡 helpful hints throughout

#### Conversational Tone

- Friendly, encouraging language
- Pie-chan personality with pie emoji 🥧
- Clear instructions with examples
- Positive reinforcement

### 5. Technical Implementation

#### State Management

```javascript
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
```

#### API Integration

- **Endpoint**: `../includes/ai_add_task.php`
- **Method**: POST
- **Data Format**: JSON with title, description, deadline, priority_id, status_id
- **Response**: "success" or error message

#### Priority/Status Mapping

```javascript
const priorityMap = { High: 3, Medium: 2, Low: 1 };
const statusMap = { "To Do": 1, "In Progress": 2, Done: 3 };
```

### 6. Example Conversation Flow

```
User: "I want to create a task"
Pie-chan: "Hi there! 🥧 I'd love to help you create a task! Let's go through this step by step.

Step 1 of 5: Task Title
What would you like to name your task?
💡 Tips: Keep it clear and specific..."

User: "Complete website redesign"
Pie-chan: "Great! ✨ Your task title is: "Complete website redesign"

Step 2 of 5: Description
Now, can you give me a brief description..."

User: "Redesign the homepage and contact page with new branding"
Pie-chan: "Perfect! 🥧 Your description: "Redesign the homepage and contact page with new branding"

Step 3 of 5: Deadline
When do you need to complete this task?
💡 Please use this format: YYYY-MM-DD HH:MM..."

User: "2025-08-15 17:00"
Pie-chan: "Excellent! 🥧 Your deadline is: 2025-08-15 17:00

Step 4 of 5: Priority
How important is this task?
🔴 High - Urgent and important..."

User: "High"
Pie-chan: "Perfect! 🔴 Your priority is: High

Step 5 of 5: Status
What's the current status of this task?
📋 To Do - Haven't started yet..."

User: "To Do"
Pie-chan: "Great! 📋 Your status is: To Do

📋 Task Summary:
✨ Title: Complete website redesign
📝 Description: Redesign the homepage and contact page with new branding
⏰ Deadline: 2025-08-15 17:00
⚡ Priority: High
📊 Status: To Do
🖼️ Image: Default image

Everything looks good? 🥧
- Type 'yes' or 'confirm' to create the task..."

User: "yes"
Pie-chan: "🎉 Woohoo! Your task has been created successfully!

Your task "Complete website redesign" is now in your task list..."
```

### 7. Benefits

1. **User-Friendly**: Step-by-step guidance prevents confusion
2. **Validation**: Ensures all data is correct before submission
3. **Flexible**: Accepts various input formats and allows cancellation
4. **Engaging**: Conversational tone makes the process enjoyable
5. **Comprehensive**: Covers all required task fields with proper validation
6. **Error-Proof**: Clear error messages and recovery options

### 8. Integration with Existing System

The chatbot integrates seamlessly with:

- **Database**: Uses existing task tables and relationships
- **Authentication**: Respects user sessions
- **UI**: Works with existing Bootstrap and chatbot components
- **API**: Uses established endpoints for task creation

This enhanced chatbot provides a modern, user-friendly way to create tasks while maintaining the fun, helpful personality of Pie-chan! 🥧✨
