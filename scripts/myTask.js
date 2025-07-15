      // Task management functionality
        let tasks = [];
        let currentTaskId = null;
        let taskIdCounter = 1;

        // Priority colors
        const priorityColors = {
            'Low': '#6c757d',
            'Moderate': '#17a2b8',
            'High': '#ffc107',
            'Extreme': '#ff6b6b'
        };

        // Status colors
        const statusColors = {
            'Not Started': '#ff6b6b',
            'In Progress': '#ffc107',
            'Completed': '#28a745'
        };

        // Add task functionality
        document.getElementById('addTaskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const title = document.getElementById('newTaskTitle').value;
            const objective = document.getElementById('newTaskObjective').value;
            const description = document.getElementById('newTaskDescription').value;
            const deadline = document.getElementById('newTaskDeadline').value;
            const priority = document.getElementById('newTaskPriority').value;
            const status = document.getElementById('newTaskStatus').value;
            const notes = document.getElementById('newTaskNotes').value;
            const image = document.getElementById('newTaskImage').files[0];

            if (title.trim() === '') {
                alert('Please enter a task title');
                return;
            }

            const newTask = {
                id: taskIdCounter++,
                title: title,
                objective: objective,
                description: description,
                deadline: deadline,
                priority: priority,
                status: status,
                notes: notes,
                image: image ? URL.createObjectURL(image) : null,
                createdDate: new Date().toLocaleDateString('en-GB')
            };

            tasks.push(newTask);
            renderTasks();
            
            // Clear form
            document.getElementById('addTaskForm').reset();
            document.getElementById('newTaskImagePreview').style.display = 'none';
            
            // Clear form
            document.getElementById('addTaskForm').reset();
            document.getElementById('newTaskImagePreview').style.display = 'none';
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addTaskModal')).hide();
        });

        // Render tasks
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
            
            taskContainer.innerHTML = tasks.map(task => `
                <div class="card mb-3 border-0 shadow-sm task-item" 
                     style="border-radius:12px; background:#f8f9fa; cursor: pointer;"
                     onclick="selectTask(${task.id})">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:12px; height:12px; border:2px solid ${priorityColors[task.priority]};">
                                </div>
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
                                                <span style="color:${priorityColors[task.priority]};">${task.priority}</span>
                                            </small>
                                            <small class="text-muted">Status: 
                                                <span style="color:${statusColors[task.status]};">${task.status}</span>
                                            </small>
                                            <small class="text-muted">Created: ${task.createdDate}</small>
                                        </div>
                                    </div>
                                    <div class="ms-2">
                                        <img src="${task.image || 'https://via.placeholder.com/50x35/e9ecef/666?text=Task'}"
                                             alt="Task preview" class="rounded"
                                             style="width:50px; height:35px; object-fit:cover;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Select task
        function selectTask(taskId) {
            currentTaskId = taskId;
            const task = tasks.find(t => t.id === taskId);
            
            // Remove active class from all tasks
            document.querySelectorAll('.task-item').forEach(item => {
                item.classList.remove('border-primary');
                item.style.borderWidth = '0';
            });
            
            // Add active class to selected task
            event.currentTarget.classList.add('border-primary');
            event.currentTarget.style.borderWidth = '2px';
            
            showTaskDetails(task);
        }

        // Show task details
        function showTaskDetails(task) {
            const emptyDetails = document.getElementById('emptyDetails');
            const taskDetails = document.getElementById('taskDetails');
            
            emptyDetails.style.display = 'none';
            taskDetails.style.display = 'block';
            
            taskDetails.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0" style="color:#333;">${task.title}</h4>
                    <div>
                        <button class="btn btn-sm rounded-pill me-2"
                                style="background:#ff6b6b; color:white;" 
                                onclick="showDeleteModal(${task.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button class="btn btn-sm rounded-pill"
                                style="background:#ff6b6b; color:white;" 
                                onclick="showEditModal(${task.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <span class="me-2" style="color:#666;">Priority:</span>
                            <span class="badge rounded-pill"
                                  style="background:${priorityColors[task.priority]}; color:white;">${task.priority}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <span class="me-2" style="color:#666;">Status:</span>
                            <span class="badge rounded-pill"
                                  style="background:${statusColors[task.status]}; color:white;">${task.status}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Created on: ${task.createdDate}</small>
                </div>

                <div class="mb-4">
                    ${task.objective ? `
                        <div class="mb-3">
                            <strong style="color:#333;">Objective:</strong>
                            <span class="text-muted ms-2">${task.objective}</span>
                        </div>
                    ` : ''}

                    ${task.description ? `
                        <div class="mb-3">
                            <strong style="color:#333;">Task Description:</strong>
                            <p class="text-muted mt-2" style="font-size:0.95rem; line-height:1.6;">
                                ${task.description}
                            </p>
                        </div>
                    ` : ''}

                    ${task.deadline ? `
                        <div class="mb-3">
                            <strong style="color:#333;">Deadline:</strong>
                            <span class="text-muted ms-2">${task.deadline}</span>
                        </div>
                    ` : ''}

                    ${task.notes ? `
                        <div class="mb-3">
                            <strong style="color:#333;">Additional Notes:</strong>
                            <p class="text-muted mt-2" style="font-size:0.95rem; line-height:1.6;">
                                ${task.notes}
                            </p>
                        </div>
                    ` : ''}

                    ${task.image ? `
                        <div class="mb-3">
                            <strong style="color:#333;">Task Image:</strong>
                            <div class="mt-2">
                                <img src="${task.image}" alt="Task image" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        }

        // Show empty details
        function showEmptyDetails() {
            const emptyDetails = document.getElementById('emptyDetails');
            const taskDetails = document.getElementById('taskDetails');
            
            emptyDetails.style.display = 'block';
            taskDetails.style.display = 'none';
        }

        // Show edit modal
        function showEditModal(taskId) {
            const task = tasks.find(t => t.id === taskId);
            
            document.getElementById('editTaskTitle').value = task.title;
            document.getElementById('editTaskObjective').value = task.objective || '';
            document.getElementById('editTaskDescription').value = task.description || '';
            document.getElementById('editTaskDeadline').value = task.deadline || '';
            document.getElementById('editTaskPriority').value = task.priority;
            document.getElementById('editTaskStatus').value = task.status;
            document.getElementById('editTaskNotes').value = task.notes || '';
            
            new bootstrap.Modal(document.getElementById('editTaskModal')).show();
        }

        // Edit task functionality
        document.getElementById('editTaskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const task = tasks.find(t => t.id === currentTaskId);
            if (!task) return;
            
            task.title = document.getElementById('editTaskTitle').value;
            task.objective = document.getElementById('editTaskObjective').value;
            task.description = document.getElementById('editTaskDescription').value;
            task.deadline = document.getElementById('editTaskDeadline').value;
            task.priority = document.getElementById('editTaskPriority').value;
            task.status = document.getElementById('editTaskStatus').value;
            task.notes = document.getElementById('editTaskNotes').value;
            
            // Handle image upload in edit form
            const imageFile = document.getElementById('editTaskImage').files[0];
            if (imageFile) {
                task.image = URL.createObjectURL(imageFile);
            }
            
            renderTasks();
            showTaskDetails(task);
            
            // Clear preview
            document.getElementById('editTaskImagePreview').style.display = 'none';
            
            bootstrap.Modal.getInstance(document.getElementById('editTaskModal')).hide();
        });

        // Show delete modal
        function showDeleteModal(taskId) {
            currentTaskId = taskId;
            new bootstrap.Modal(document.getElementById('deleteTaskModal')).show();
        }

        // Delete task functionality
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            tasks = tasks.filter(t => t.id !== currentTaskId);
            renderTasks();
            
            bootstrap.Modal.getInstance(document.getElementById('deleteTaskModal')).hide();
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