# Notification System Documentation

## Overview

The notification system has been implemented to provide real-time alerts for pending, overdue, and upcoming tasks in the EasyPi task management application.

## Features

### 1. Visual Notification Bell

- **Bell Icon**: Shows a filled bell when there are pending tasks, empty bell when none
- **Badge Counter**: Displays the number of pending tasks (shows "9+" for more than 9 tasks)
- **Color Coding**:
  - Red: Pending tasks exist
  - Gray: No pending tasks

### 2. Interactive Dropdown

- **Task List**: Shows up to 5 most urgent tasks
- **Priority Indicators**: Color-coded badges (High: red, Medium: yellow, Low: gray)
- **Due Date Status**:
  - Overdue tasks marked in red
  - Today's tasks marked in yellow
  - Future tasks marked in blue
- **Status Badges**: Shows current task status (Not Started, In Progress, Completed)

### 3. Task Categories

- **Overdue Tasks**: Tasks past their due date
- **Today's Tasks**: Tasks due today
- **Upcoming Tasks**: Tasks due in the near future

### 4. Auto-Refresh

- Notifications refresh every 5 minutes automatically
- Manual refresh when dropdown is opened

### 5. Click Actions

- Clicking on a task notification redirects to the task details page
- "View all tasks" link for when there are more than 5 pending tasks

## Files Structure

### Backend Files

- `functions/taskFunctions.php` - Core functions for fetching task data
- `includes/get_notifications.php` - AJAX endpoint for notification data
- `includes/check_deadlines.php` - Checks for upcoming deadlines
- `includes/mark_notifications_read.php` - Marks notifications as read

### Frontend Files

- `components/notifications.php` - Main notification dropdown component
- `components/header.php` - Updated header with notification integration
- `scripts/notifications.js` - JavaScript for notification interactions

## Database Integration

The system uses the existing database structure:

- `tasks` table - Main task data
- `task_status` table - Task statuses (Not Started, In Progress, Completed)
- `task_priority_levels` table - Priority levels (Low, Medium, High)
- `accounts` table - User accounts for task filtering

## Key Functions

### PHP Functions

- `getPendingTasksForToday()` - Gets tasks due today or overdue
- `getOverdueTasksCount()` - Counts overdue tasks
- `getTodayTasksCount()` - Counts tasks due today
- `getNotificationData()` - Comprehensive notification data
- `renderNotificationDropdown()` - Generates the notification HTML

### JavaScript Functions

- `initializeNotifications()` - Initializes the notification system
- `refreshNotifications()` - Refreshes notification data via AJAX
- `viewTaskDetails(taskId)` - Redirects to task details
- `checkUpcomingDeadlines()` - Checks for upcoming deadlines
- `showNotificationToast()` - Shows toast notifications

## Usage

### Basic Implementation

The notification system is automatically included in the header component:

```php
<?php include_once __DIR__ . '/components/header.php'; ?>
```

### Customization

You can customize the notification behavior by modifying:

- `$displayLimit` in `notifications.php` to change how many tasks are shown
- Refresh interval in `notifications.js` (currently 5 minutes)
- Priority colors in the CSS section of `notifications.php`

### AJAX Endpoints

- `GET /includes/get_notifications.php` - Fetch notification data
- `GET /includes/check_deadlines.php` - Check upcoming deadlines
- `POST /includes/mark_notifications_read.php` - Mark notifications as read

## Security

- All database queries use prepared statements
- Session validation for authenticated users only
- Input sanitization with `htmlspecialchars()`
- Error logging for debugging without exposing sensitive information

## Future Enhancements

- Push notifications for browser support
- Email notifications for overdue tasks
- Customizable notification preferences
- Task reminder system
- Mobile app notifications
- Real-time updates using WebSockets

## Troubleshooting

### Common Issues

1. **Notifications not showing**: Check if user is logged in and session is active
2. **Dropdown not working**: Ensure Bootstrap JavaScript is loaded
3. **Database errors**: Check database connection and table structure
4. **AJAX failures**: Verify file paths and server configuration

### Debug Mode

Enable error logging in PHP configuration and check logs for detailed error messages.

## Installation Notes

1. Ensure Bootstrap 5.3.7 and Bootstrap Icons are included
2. Database must have the required tables with proper foreign key relationships
3. Session management must be properly configured
4. File permissions should allow read/write access for log files

## Browser Compatibility

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Internet Explorer 11 (limited support)

## Performance Considerations

- Notifications cache for 5 minutes to reduce database load
- Limit of 5 tasks shown in dropdown to prevent UI clutter
- Efficient database queries with proper indexing
- AJAX requests are throttled to prevent spam

This notification system provides a comprehensive solution for keeping users informed about their task deadlines and priorities in the EasyPi application.
