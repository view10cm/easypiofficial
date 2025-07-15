let tasks = [];
let currentTaskId = null;
let taskIdCounter = 1;

const statusColors = {
    'Not Started': '#ff6b6b',
    'In Progress': '#17a2b8',
    'Completed': '#28a745'
};

document.addEventListener('DOMContentLoaded', loadTasksFromDB);

async function loadTasksFromDB() {
    try {
        const response = await fetch('../includes/get_tasks.php');
        const result = await response.json();

        if (!result.success) {
            console.error(result.message || 'Failed to fetch tasks.');
            return;
        }

        tasks = result.tasks.map(task => {
            const isUploadedImage = task.task_img && task.task_img.startsWith('task_');
            const imagePath = isUploadedImage
                ? `../uploads/tasks/${task.task_img}`
                : `../assets/${task.task_img || 'working.png'}`;

            return {
                id: task.task_id,
                title: task.task_title,
                description: task.task_description,
                deadline: task.task_due_date,
                priority: task.priority_name,
                status: task.status_name,
                image: imagePath,
                createdDate: new Date(task.created_at).toLocaleDateString('en-GB')
            };
        });

        renderTasks();
    } catch (error) {
        console.error('Error loading tasks:', error);
    }
}

function setMinDateTime() {
    const now = new Date();

    const yyyy = now.getFullYear();
    const mm = String(now.getMonth() + 1).padStart(2, '0');
    const dd = String(now.getDate()).padStart(2, '0');
    const hh = String(now.getHours()).padStart(2, '0');
    const min = String(now.getMinutes()).padStart(2, '0');

    const minDateTime = `${yyyy}-${mm}-${dd}T${hh}:${min}`;

    const deadlineInput = document.getElementById('newTaskDeadline');
    if (deadlineInput) {
        deadlineInput.min = minDateTime;
    }
}

document.addEventListener('DOMContentLoaded', setMinDateTime);

// Add Task Form Submission
const addForm = document.getElementById('addTaskForm');
const submitBtn = document.getElementById('addTaskBtn');

addForm.addEventListener('submit', async function (e) {
    e.preventDefault();

    const title = document.getElementById('newTaskTitle').value.trim();
    const description = document.getElementById('newTaskDescription').value.trim();
    const deadline = document.getElementById('newTaskDeadline').value;
    const priority = document.getElementById('newTaskPriority').value;
    const status = document.getElementById('newTaskStatus').value;
    const image = document.getElementById('newTaskImage').files[0];

    if (!title) {
        alert('Please enter a task title');
        return;
    }

    // Disable the button and show loading
    submitBtn.disabled = true;
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Adding Task...';

    const formData = new FormData();
    formData.append('title', title);
    formData.append('description', description);
    formData.append('deadline', deadline);
    formData.append('priority', priority);
    formData.append('status', status);
    if (image) {
        formData.append('image', image);
    }

    try {
        await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate 1s delay

        const response = await fetch('../includes/add_task.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (!result.success) {
            alert(result.message || 'Task could not be saved.');
            return;
        }

        await loadTasksFromDB();

        addForm.reset();
        document.getElementById('newTaskImagePreview').style.display = 'none';
        bootstrap.Modal.getInstance(document.getElementById('addTaskModal')).hide();
    } catch (error) {
        console.error('Error:', error);
        alert('Something went wrong while adding the task.');
    } finally {
        // Re-enable the button and restore text
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
});



function renderTasks() {
  const taskContainer = document.getElementById('taskContainer');
  const emptyState = document.getElementById('emptyState');

  if (tasks.length === 0) {
    taskContainer.style.display = 'none';
    emptyState.style.display = 'block';
    showEmptyDetails();
    return;
  }

  emptyState.style.display = 'none';
  taskContainer.style.display = 'block';

  // Sort tasks by deadline (earliest first)
  const sortedTasks = [...tasks].sort((a, b) => new Date(a.deadline) - new Date(b.deadline));

  taskContainer.innerHTML = sortedTasks.map(task => {
    const cleanPriority = task.priority.trim();
    const cleanStatus = task.status.trim();
    const statusColor = statusColors[cleanStatus] || '#6c757d';

    return `
      <div class="card mb-3 border-0 shadow-sm task-item" 
           data-task-id="${task.id}"
           style="border-radius:12px; background:#f8f9fa; cursor:pointer;">
        <div class="card-body p-3">
          <div class="row align-items-center">
            <div class="col-auto">
              <div class="rounded-circle d-flex align-items-center justify-content-center"
                   style="width:12px; height:12px; border:2px solid ${statusColor};"></div>
            </div>
            <div class="col">
              <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                  <h6 class="mb-1 fw-semibold" style="color:#333;">${task.title}</h6>
                  <p class="text-muted mb-2" style="font-size:0.9rem;">
                    ${task.description ? task.description.substring(0, 50) + '...' : 'No description'}
                  </p>
                  <div class="d-flex gap-3">
                    <small class="text-muted">Priority: 
                      <span>${cleanPriority}</span>
                    </small>
                    <small class="text-muted">Status: 
                      <span style="color:${statusColor};">${cleanStatus}</span>
                    </small>
                    <small class="text-muted">Deadline: ${task.deadline}</small>
                  </div>
                </div>
                <div class="ms-2">
                  <img src="${task.image}" alt="Task preview" class="rounded"
                       style="width:50px; height:35px; object-fit:cover;">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }).join('');


    // Attach click handlers
    document.querySelectorAll('.task-item').forEach(item => {
        item.addEventListener('click', function (e) {
            const taskId = parseInt(this.dataset.taskId);
            selectTask(e, taskId);
        });
    });
}

function selectTask(event, taskId) {
    currentTaskId = taskId;
    const task = tasks.find(t => t.id === taskId);

    document.querySelectorAll('.task-item').forEach(item => {
        item.classList.remove('border-primary');
        item.style.borderWidth = '0';
    });

    event.currentTarget.classList.add('border-primary');
    event.currentTarget.style.borderWidth = '2px';

    showTaskDetails(task);
}

function showTaskDetails(task) {
    const emptyDetails = document.getElementById('emptyDetails');
    const taskDetails = document.getElementById('taskDetails');

    emptyDetails.style.display = 'none';
    taskDetails.style.display = 'block';

    taskDetails.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0" style="color:#333;">${task.title}</h4>
            <div>
               <button id="deleteTaskBtn" class="btn btn-sm me-2" style="background:#ff6b6b; color:white;">
                    <i class="bi bi-trash"></i>
                </button>
                <button id="editTaskBtn" class="btn btn-sm" style="background:#1286cc; color:white;">
                    <i class="bi bi-pencil"></i>
                </button>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <div class="d-flex align-items-center">
                    <span class="me-2" style="color:#666;">Priority:</span>
                    <span style="font-weight: bold">${task.priority}</span>
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex align-items-center">
                    <span class="me-2" style="color:#666;">Status:</span>
                    <span class="badge rounded-pill" style="background:${statusColors[task.status]}; color:white;">${task.status}</span>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <small class="text-muted">Created on: ${task.createdDate}</small>
        </div>

        ${task.description ? `
            <div class="mb-3">
                <strong style="color:#333;">Task Description:</strong>
                <p class="text-muted mt-2" style="font-size:0.95rem; line-height:1.6;">${task.description}</p>
            </div>` : ''}

        ${task.deadline ? `
            <div class="mb-3">
                <strong style="color:#333;">Deadline:</strong>
                <span class="text-muted ms-2">${task.deadline}</span>
            </div>` : ''}

        <div class="mb-3">
            <strong style="color:#333;">Task Image:</strong>
            <div class="mt-2">
                <img src="${task.image}" alt="Task image" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
            </div>
        </div>
    `;
     document.getElementById('editTaskBtn').addEventListener('click', () => showEditModal(task.id));
    document.getElementById('deleteTaskBtn').addEventListener('click', () => showDeleteModal(task.id));
}

function showEmptyDetails() {
    document.getElementById('emptyDetails').style.display = 'block';
    document.getElementById('taskDetails').style.display = 'none';
}
function setEditMinDateTime() {
    const now = new Date();

    const yyyy = now.getFullYear();
    const mm = String(now.getMonth() + 1).padStart(2, '0');
    const dd = String(now.getDate()).padStart(2, '0');
    const hh = String(now.getHours()).padStart(2, '0');
    const min = String(now.getMinutes()).padStart(2, '0');

    const minDateTime = `${yyyy}-${mm}-${dd}T${hh}:${min}`;

    const input = document.getElementById('editTaskDeadline');
    if (input) input.min = minDateTime;
}

document.addEventListener('DOMContentLoaded', setEditMinDateTime);


        // Show edit modal
function showEditModal(taskId) {
  const task = tasks.find(t => t.id === taskId);
  if (!task) return;

  currentTaskId = taskId;

  document.getElementById('editTaskId').value = taskId;
  document.getElementById('editTaskTitle').value = task.title;
  document.getElementById('editTaskDescription').value = task.description || '';
  document.getElementById('editTaskDeadline').value = task.deadline || '';
  document.getElementById('editTaskPriority').value = task.priority;
  document.getElementById('editTaskStatus').value = task.status;

  if (task.image) {
    document.getElementById('editTaskImagePreview').style.display = 'block';
    document.querySelector('#editTaskImagePreview img').src = task.image;
  } else {
    document.getElementById('editTaskImagePreview').style.display = 'none';
  }

  new bootstrap.Modal(document.getElementById('editTaskModal')).show();
}

// Submit Edited Task
const editForm = document.getElementById('editTaskForm');
const editBtn = document.getElementById('editTaskBtn');

editForm.addEventListener('submit', async function (e) {
  e.preventDefault();

  const taskId = document.getElementById('editTaskId').value;
  const formData = new FormData();
  formData.append('task_id', taskId);
  formData.append('title', document.getElementById('editTaskTitle').value.trim());
  formData.append('description', document.getElementById('editTaskDescription').value.trim());
  formData.append('deadline', document.getElementById('editTaskDeadline').value);
  formData.append('priority', document.getElementById('editTaskPriority').value);
  formData.append('status', document.getElementById('editTaskStatus').value);

  const image = document.getElementById('editTaskImage').files[0];
  if (image) formData.append('image', image);

  // Show loading state
  editBtn.disabled = true;
  const originalText = editBtn.textContent;
  editBtn.textContent = 'Saving Changes...';

  try {
    await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate delay

    const res = await fetch('../includes/update_task.php', {
      method: 'POST',
      body: formData
    });

    const result = await res.json();
    if (result.success) {
      await loadTasksFromDB(); // Refresh task list
      const updatedTask = tasks.find(t => t.id == taskId);
      if (updatedTask) {
        showTaskDetails(updatedTask); // Update sidebar
      }
      bootstrap.Modal.getInstance(document.getElementById('editTaskModal')).hide();
    } else {
      alert(result.message || 'Update failed.');
    }
  } catch (err) {
    console.error('Edit error:', err);
    alert('Something went wrong.');
  } finally {
    editBtn.disabled = false;
    editBtn.textContent = originalText;
  }
});

        function showDeleteModal(taskId) {
  currentTaskId = taskId;
  new bootstrap.Modal(document.getElementById('deleteTaskModal')).show();
}

const deleteBtn = document.getElementById('confirmDeleteBtn');

deleteBtn.addEventListener('click', async function () {
  // Show loading state
  deleteBtn.disabled = true;
  const originalText = deleteBtn.textContent;
  deleteBtn.textContent = 'Deleting...';

  try {
    await new Promise(resolve => setTimeout(resolve, 1000)); // Optional simulated delay

    const res = await fetch('../includes/delete_task.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ task_id: currentTaskId })
    });

    const result = await res.json();

    if (result.success) {
      // Remove task from array
      tasks = tasks.filter(t => t.id !== currentTaskId);

      // Re-render task list
      renderTasks();

      // Reset task detail panel
      document.getElementById('taskDetails').innerHTML = '';
      document.getElementById('taskDetails').style.display = 'none';
      document.getElementById('emptyDetails').style.display = 'block';

      // Close modal
      bootstrap.Modal.getInstance(document.getElementById('deleteTaskModal')).hide();
    } else {
      alert(result.message || 'Failed to delete the task.');
    }
  } catch (err) {
    console.error('AJAX Delete Error:', err);
    alert('Something went wrong.');
  } finally {
    deleteBtn.disabled = false;
    deleteBtn.textContent = originalText;
  }
});




        // Image preview for new task
        document.getElementById('newTaskImage').addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('newTaskImagePreview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.querySelector('img').src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Image preview for edit task
        document.getElementById('editTaskImage').addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('editTaskImagePreview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.querySelector('img').src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Image preview for new task
        document.getElementById('newTaskImage').addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('newTaskImagePreview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.querySelector('img').src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Image preview for edit task
        document.getElementById('editTaskImage').addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('editTaskImagePreview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.querySelector('img').src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Initial render
        renderTasks();