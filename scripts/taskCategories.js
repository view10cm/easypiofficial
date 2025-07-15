 let currentEditPriorityRow = null;

function showEditPriorityModal(btn) {
  currentEditPriorityRow = btn.closest('tr');
  const name = currentEditPriorityRow.cells[1].textContent.trim();
  const id = currentEditPriorityRow.getAttribute('data-priority-id');

  // Populate modal fields
  document.getElementById('editTaskPriorityName').value = name;
  document.getElementById('editPriorityId').value = id;

  // Show the modal
  const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editTaskPriorityModal'));
  modal.show();
}

document.getElementById('editPriorityForm').addEventListener('submit', async function (e) {
  e.preventDefault();

  const newName = document.getElementById('editTaskPriorityName').value.trim();
  const priorityId = document.getElementById('editPriorityId').value;
  const saveBtn = this.querySelector('button[type="submit"]');

  if (!newName || !priorityId) {
    alert("Please enter a valid priority name.");
    return;
  }

  // Show loading state
  const originalText = saveBtn.textContent;
  saveBtn.disabled = true;
  saveBtn.textContent = 'Saving...';

  // 1 second delay
  await new Promise(resolve => setTimeout(resolve, 1000));

  try {
    const formData = new URLSearchParams();
    formData.append('priority_id', priorityId);
    formData.append('priority_name', newName);

    const response = await fetch('../includes/edit_priority.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      if (currentEditPriorityRow) {
        currentEditPriorityRow.cells[1].textContent = newName;
      }

      bootstrap.Modal.getInstance(document.getElementById('editTaskPriorityModal')).hide();
    } else {
      alert(result.message || 'Failed to update priority.');
    }

  } catch (error) {
    console.error(error);
    alert("AJAX error while saving priority.");
  } finally {
    saveBtn.disabled = false;
    saveBtn.textContent = originalText;
  }
});


const addPriorityForm = document.getElementById('addPriorityForm');
const addPriorityBtn = document.getElementById('addPriorityBtn');

let selectedPriorityId = null;
let selectedPriorityRow = null;

addPriorityForm.addEventListener('submit', async function (e) {
  e.preventDefault();

  const input = document.getElementById('taskPriorityName');
  const value = input.value.trim();

  if (!value) {
    alert('Please enter a priority name.');
    return;
  }

  addPriorityBtn.disabled = true;
  const originalText = addPriorityBtn.textContent;
  addPriorityBtn.textContent = 'Adding Priority...';

  await new Promise(resolve => setTimeout(resolve, 1000));

  const formData = new FormData();
  formData.append('priority', value);

  try {
    const response = await fetch('../includes/add_priority.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (!result.success) {
      alert(result.message || 'Failed to add priority.');
      return;
    }

    const tableBody = document.querySelector('#taskPriorityTable tbody');
const noRow = document.getElementById('noTaskPriorityRow');
if (noRow) noRow.remove();

const rowCount = tableBody.querySelectorAll('tr').length + 1;

const newRow = document.createElement('tr');
newRow.setAttribute('data-priority-id', result.priority_id);
newRow.innerHTML = `
  <td class="text-center">${rowCount}</td>
  <td class="fw-bold text-center">${result.priority_name}</td>
  <td class="text-center">
    <button class="edit-btn btn btn-sm btn-warning" data-id="${result.priority_id}">
      <i class="bi bi-pencil-square"></i> Edit
    </button>
    <button class="delete-btn btn btn-sm btn-danger ms-2" data-id="${result.priority_id}">
      <i class="bi bi-trash"></i> Delete
    </button>
  </td>
`;

tableBody.appendChild(newRow);

// Attach listeners for edit and delete
newRow.querySelector('.edit-btn').addEventListener('click', function () {
  showEditPriorityModal(this);
});

newRow.querySelector('.delete-btn').addEventListener('click', function () {
  deletePriorityRow(this);
});

// Reset form and close modal
addPriorityForm.reset();
bootstrap.Modal.getInstance(document.getElementById('addTaskPriorityModal')).hide();
  } catch (error) {
    console.error(error);
    alert('Something went wrong while adding the priority.');
  } finally {
    addPriorityBtn.disabled = false;
    addPriorityBtn.textContent = originalText;
  }
});

// Handle delete button click
document.addEventListener('click', function (e) {
  if (e.target.closest('.delete-btn')) {
    const btn = e.target.closest('.delete-btn');
    selectedPriorityId = btn.getAttribute('data-id');
    selectedPriorityRow = btn.closest('tr');
    const modal = new bootstrap.Modal(document.getElementById('deletePriorityModal'));
    modal.show();
  }
});

// Confirm delete
document.getElementById('confirmDeletePriorityBtn').addEventListener('click', async () => {
  if (!selectedPriorityId) return;

  const deleteBtn = document.getElementById('confirmDeletePriorityBtn');
  const originalText = deleteBtn.textContent;
  deleteBtn.disabled = true;
  deleteBtn.textContent = 'Deleting...';

  // Optional delay (simulate or wait for animation)
  await new Promise(resolve => setTimeout(resolve, 1000));

  try {
    const response = await fetch('../includes/delete_priority.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `priority_id=${encodeURIComponent(selectedPriorityId)}`
    });

    const result = await response.json();

    if (result.success && selectedPriorityRow) {
      selectedPriorityRow.remove();

      const tableBody = document.querySelector('#taskPriorityTable tbody');
      const rows = tableBody.querySelectorAll('tr');

      if (rows.length === 0) {
        const emptyRow = document.createElement('tr');
        emptyRow.id = 'noTaskPriorityRow';
        emptyRow.innerHTML = `<td colspan="3" class="text-center text-muted">No Task Priority found.</td>`;
        tableBody.appendChild(emptyRow);
      } else {
        tableBody.querySelectorAll('tr').forEach((r, i) => {
          const numCell = r.querySelector('td');
          if (numCell) numCell.textContent = i + 1;
        });
      }
    } else {
      alert(result.message || 'Failed to delete priority.');
    }
  } catch (err) {
    console.error(err);
    alert('Error while deleting priority.');
  } finally {
    deleteBtn.disabled = false;
    deleteBtn.textContent = originalText;

    const modal = bootstrap.Modal.getInstance(document.getElementById('deletePriorityModal'));
    modal.hide();
    selectedPriorityId = null;
    selectedPriorityRow = null;
  }
});


document.addEventListener('DOMContentLoaded', function () {
  // Attach edit modal logic to existing buttons
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function () {
      showEditPriorityModal(this);
    });
  });

  // Attach delete logic if needed too
  document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function () {
      deletePriorityRow(this);
    });
  });
});
