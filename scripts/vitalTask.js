function loadTaskDetails(taskId) {
  fetch(`../includes/fetch_tasks.php?task_id=${taskId}`)
    .then(response => {
      if (!response.ok) throw new Error('Failed to fetch task details');
      return response.json();
    })
    .then(task => {
      const statusColors = {
        'Not Started': '#ff6b6b',
        'In Progress': '#17a2b8',
        'Completed': '#28a745'
      };

      document.getElementById('task-title').textContent = task.task_title;
      document.getElementById('task-description').textContent = task.task_description;
      document.getElementById('task-priority').textContent = task.priority_name || 'Unknown';
      document.getElementById('task-status').textContent = task.status_name || 'Unknown';
      document.getElementById('task-deadline').textContent = new Date(task.task_due_date).toLocaleString();
      document.getElementById('task-img').src = task.task_img 
          ? `../uploads/${task.task_img}` 
          : '../assets/working.png';

      document.getElementById('task-status').style.background = statusColors[task.status_name] || '#6c757d';
      document.getElementById('task-priority').style.background = '#6c757d';

      document.getElementById('emptyDetails').classList.add('d-none');
      document.getElementById('taskDetails').classList.remove('d-none');
    })
    .catch(error => {
      console.error(error);
      alert('Failed to load task details.');
    });
}

