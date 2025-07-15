document.getElementById('signupForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const registerBtn = document.getElementById('registerBtn');
    registerBtn.disabled = true;
    registerBtn.textContent = 'Registering...';

    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const agreeTerms = document.getElementById('agreeTerms').checked;

    function resetButton() {
      registerBtn.disabled = false;
      registerBtn.textContent = 'Register';
    }

    // Required fields
    if (!username || !email || !password || !confirmPassword) {
      alert('All fields are required.');
      return resetButton();
    }

    // Username validation
    if (username.startsWith(' ') || /\s/.test(username)) {
      alert('Username cannot start with or contain spaces.');
      return resetButton();
    }
    const usernameRegex = /^[a-zA-Z0-9_]{4,20}$/;
    if (!usernameRegex.test(username)) {
      alert('Username must be 4–20 characters and only use letters, numbers, or underscores.');
      return resetButton();
    }

    // Email validation
    if (email.startsWith(' ') || /\s/.test(email)) {
      alert('Email cannot start with or contain spaces.');
      return resetButton();
    }
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert('Enter a valid email address.');
      return resetButton();
    }

    // Password checks
    if (password !== confirmPassword) {
      alert('Passwords do not match.');
      return resetButton();
    }

    if (!agreeTerms) {
      alert('You must agree to the terms.');
      return resetButton();
    }

    const passwordStrengthRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    if (!passwordStrengthRegex.test(password)) {
      alert('Password must have at least 8 characters, one uppercase letter, one number, and one special character.');
      return resetButton();
    }

    const formData = new FormData();
    formData.append('username', username.trim());
    formData.append('email', email.trim());
    formData.append('password', password);

    try {
      const response = await fetch('../includes/register_account.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();
      console.log(result);

      if (result.success) {
        registerBtn.textContent = 'Redirecting...';
        setTimeout(() => {
          window.location.href = 'sign_in.html?registered=1';
        }, 1000);
      } else {
        // Reset form only if it's a duplicate username or email
        const duplicateMessage = 'Username or email already exists.';
        if (result.message && result.message.toLowerCase().includes('exists')) {
          document.getElementById('signupForm').reset();
        }
        alert(result.message);
        resetButton();
      }
    } catch (error) {
      console.error(error);
      alert('An error occurred during registration.');
      resetButton();
    }
  });