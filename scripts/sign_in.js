const params = new URLSearchParams(window.location.search);
  if (params.get('registered') === '1') {
    const messageDiv = document.createElement('div');
    messageDiv.textContent = 'Account created successfully. You can now log in.';
    messageDiv.style.backgroundColor = '#d4edda';
    messageDiv.style.color = '#155724';
    messageDiv.style.padding = '12px';
    messageDiv.style.border = '1px solid #c3e6cb';
    messageDiv.style.borderRadius = '6px';
    messageDiv.style.marginBottom = '15px';
    messageDiv.style.textAlign = 'center';

    const form = document.querySelector('form');
    if (form) {
      form.parentNode.insertBefore(messageDiv, form);
    }

    // Optional: Remove query param from URL without refreshing
    window.history.replaceState({}, document.title, window.location.pathname);
  }

document.getElementById('loginForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const loginBtn = document.getElementById('loginBtn');
  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value.trim();
  const rememberMe = document.getElementById('rememberMe').checked;

  function resetButton() {
    loginBtn.disabled = false;
    loginBtn.textContent = 'Login';
  }

  if (!username || !password) {
    alert('Both fields are required.');
    return resetButton();
  }

  // Optional: Add simple format validations here if needed

  loginBtn.disabled = true;
  loginBtn.textContent = 'Logging in...';

  fetch('../includes/login.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password, rememberMe })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      setTimeout(() => {
        window.location.href = 'dashboard.php';
      }, 3000);
    } else {
      alert(data.message);
      resetButton();
    }
  })
  .catch(err => {
    console.error('Login error:', err);
    alert('Something went wrong.');
    resetButton();
  });
});