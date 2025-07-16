// Notification system JavaScript
document.addEventListener("DOMContentLoaded", function () {
  initializeNotifications();
});

function initializeNotifications() {
  // Auto-refresh notifications every 5 minutes
  setInterval(refreshNotifications, 300000); // 5 minutes

  // Handle notification clicks
  document.addEventListener("click", function (e) {
    if (e.target.closest(".notification-item")) {
      e.preventDefault();
      const taskId = e.target.closest(".notification-item").dataset.taskId;
      if (taskId) {
        viewTaskDetails(taskId);
      }
    }
  });

  // Mark notification as read when dropdown is opened
  const notificationDropdown = document.getElementById("notificationDropdown");
  if (notificationDropdown) {
    notificationDropdown.addEventListener("shown.bs.dropdown", function () {
      markNotificationsAsRead();
    });
  }
}

function refreshNotifications() {
  // Fetch updated notification data
  fetch("../includes/get_notifications.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        updateNotificationUI(data);
      }
    })
    .catch((error) => {
      console.error("Error refreshing notifications:", error);
    });
}

function updateNotificationUI(data) {
  const notificationBtn = document.getElementById("notificationDropdown");
  const notificationDropdown = document.querySelector(".notification-dropdown");

  if (notificationBtn && notificationDropdown) {
    // Update the notification button
    const bellIcon = notificationBtn.querySelector("i");
    const badge = notificationBtn.querySelector(".badge");

    if (data.total_pending > 0) {
      notificationBtn.classList.add("text-danger");
      notificationBtn.classList.remove("text-muted");
      bellIcon.classList.add("bi-bell-fill");
      bellIcon.classList.remove("bi-bell");

      if (badge) {
        badge.textContent = data.total_pending > 9 ? "9+" : data.total_pending;
      } else {
        const newBadge = document.createElement("span");
        newBadge.className =
          "position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger";
        newBadge.textContent =
          data.total_pending > 9 ? "9+" : data.total_pending;
        notificationBtn.appendChild(newBadge);
      }
    } else {
      notificationBtn.classList.remove("text-danger");
      notificationBtn.classList.add("text-muted");
      bellIcon.classList.remove("bi-bell-fill");
      bellIcon.classList.add("bi-bell");

      if (badge) {
        badge.remove();
      }
    }

    // Update the dropdown content
    // You can implement this part to dynamically update the dropdown content
  }
}

function viewTaskDetails(taskId) {
  // Redirect to task details page
  window.location.href = `myTask.php?task_id=${taskId}`;
}

function markNotificationsAsRead() {
  // Send request to mark notifications as read
  fetch("../includes/mark_notifications_read.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      action: "mark_read",
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        console.log("Notifications marked as read");
      }
    })
    .catch((error) => {
      console.error("Error marking notifications as read:", error);
    });
}

// Function to show notification toast
function showNotificationToast(message, type = "info") {
  const toast = document.createElement("div");
  toast.className = `toast align-items-center text-white bg-${type} border-0`;
  toast.setAttribute("role", "alert");
  toast.setAttribute("aria-live", "assertive");
  toast.setAttribute("aria-atomic", "true");

  toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

  // Add to toast container or create one if it doesn't exist
  let toastContainer = document.querySelector(".toast-container");
  if (!toastContainer) {
    toastContainer = document.createElement("div");
    toastContainer.className = "toast-container position-fixed top-0 end-0 p-3";
    document.body.appendChild(toastContainer);
  }

  toastContainer.appendChild(toast);

  // Initialize and show the toast
  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();

  // Remove toast element after it's hidden
  toast.addEventListener("hidden.bs.toast", function () {
    toast.remove();
  });
}

// Check for upcoming deadlines and show notifications
function checkUpcomingDeadlines() {
  fetch("../includes/check_deadlines.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.upcoming_deadlines.length > 0) {
        data.upcoming_deadlines.forEach((task) => {
          const message = `Reminder: "${task.task_title}" is due ${
            task.days_until_due === 0
              ? "today"
              : "in " + task.days_until_due + " day(s)"
          }`;
          showNotificationToast(message, "warning");
        });
      }
    })
    .catch((error) => {
      console.error("Error checking deadlines:", error);
    });
}

// Check for deadlines when page loads
document.addEventListener("DOMContentLoaded", function () {
  checkUpcomingDeadlines();
});
