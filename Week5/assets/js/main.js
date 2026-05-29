console.log('main.js loaded — version CHECK');

// PASSWORD STRENGTH CHECKER
function checkPasswordStrength(password) {
    // Start with zero strength
    let strength = 0;

    // Each condition adds 1 point to strength
    if (password.length >= 8)           strength++;  // long enough
    if (/[A-Z]/.test(password))         strength++;  // has uppercase letter
    if (/[0-9]/.test(password))         strength++;  // has a number
    if (/[^A-Za-z0-9]/.test(password)) strength++;  // has special character

    // Get the progress bar and text elements from the page
    const bar  = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');

    // Update the bar based on strength score
    if (strength === 1) {
        bar.style.width = '25%';
        bar.className = 'progress-bar bg-danger';
        text.textContent = 'Weak';
    } else if (strength === 2) {
        bar.style.width = '50%';
        bar.className = 'progress-bar bg-warning';
        text.textContent = 'Fair';
    } else if (strength === 3) {
        bar.style.width = '75%';
        bar.className = 'progress-bar bg-info';
        text.textContent = 'Good';
    } else if (strength === 4) {
        bar.style.width = '100%';
        bar.className = 'progress-bar bg-success';
        text.textContent = 'Strong';
    } else {
        bar.style.width = '0%';
        text.textContent = '';
    }
}

// ========================================
// REGISTRATION FORM VALIDATION
// ========================================

function validateRegister() {
    // Grab what the user typed into each field
    const fullName        = document.getElementById('fullName').value.trim();
    const email           = document.getElementById('email').value.trim();
    const password        = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    // Assume no errors to start
    let isValid = true;

    // --- Validate Full Name ---
    if (fullName === '') {
        showError('fullName', 'nameError', 'Full name is required');
        isValid = false;
    } else {
        clearError('fullName', 'nameError');
    }

    // --- Validate Email ---
    // This pattern checks for a valid email format like name@domain.com
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        showError('email', 'emailError', 'Email is required');
        isValid = false;
    } else if (!emailPattern.test(email)) {
        showError('email', 'emailError', 'Please enter a valid email address');
        isValid = false;
    } else {
        clearError('email', 'emailError');
    }

    // --- Validate Password ---
    if (password === '') {
        showError('password', 'passwordError', 'Password is required');
        isValid = false;
    } else if (password.length < 8) {
        showError('password', 'passwordError', 'Password must be at least 8 characters');
        isValid = false;
    } else {
        clearError('password', 'passwordError');
    }

    // --- Validate Confirm Password ---
    if (confirmPassword === '') {
        showError('confirmPassword', 'confirmError', 'Please confirm your password');
        isValid = false;
    } else if (password !== confirmPassword) {
        showError('confirmPassword', 'confirmError', 'Passwords do not match');
        isValid = false;
    } else {
        clearError('confirmPassword', 'confirmError');
    }

    // If everything passed, send data to PHP
    if (isValid) {
        submitRegistration(fullName, email, password);
    }
}

// ========================================
// HELPER FUNCTIONS
// ========================================

function showError(inputId, errorId, message) {
    // Add red border to the input
    document.getElementById(inputId).classList.add('is-invalid');
    // Show the error message below it
    document.getElementById(errorId).textContent = message;
}

function clearError(inputId, errorId) {
    // Remove red border
    document.getElementById(inputId).classList.remove('is-invalid');
    // Hide error message
    document.getElementById(errorId).textContent = '';
}

// ========================================
// SEND DATA TO PHP USING FETCH API
// ========================================

function submitRegistration(fullName, email, password) {
    // Show loading state on button
    const btn = document.querySelector('#registerForm button');
    btn.textContent = 'Creating account...';
    btn.disabled = true;

    // Fetch API sends data to PHP without reloading the page
    fetch('/shop/includes/process_register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ fullName, email, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('formMessage').innerHTML =
                '<div class="alert alert-success">Account created! <a href="login.php">Login here</a></div>';
            document.getElementById('registerForm').reset();
        } else {
            document.getElementById('formMessage').innerHTML =
                '<div class="alert alert-danger">' + data.message + '</div>';
        }
        btn.textContent = 'Create Account';
        btn.disabled = false;
    })
    .catch(error => {
        document.getElementById('formMessage').innerHTML =
            '<div class="alert alert-danger">Something went wrong. Please try again.</div>';
        btn.textContent = 'Create Account';
        btn.disabled = false;
    });
}


// ========================================
// LOGIN FORM VALIDATION
// ========================================

function validateLogin() {
    // Get what the user typed
    const email    = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;

    // Assume valid to start
    let isValid = true;

    // --- Validate Email ---
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '') {
        showError('loginEmail', 'loginEmailError', 'Email is required');
        isValid = false;
    } else if (!emailPattern.test(email)) {
        showError('loginEmail', 'loginEmailError', 'Please enter a valid email');
        isValid = false;
    } else {
        clearError('loginEmail', 'loginEmailError');
    }

    // --- Validate Password ---
    if (password === '') {
        showError('loginPassword', 'loginPasswordError', 'Password is required');
        isValid = false;
    } else {
        clearError('loginPassword', 'loginPasswordError');
    }

    // If valid send to PHP
    if (isValid) {
        submitLogin(email, password);
    }
}

// ========================================
// SEND LOGIN DATA TO PHP
// ========================================

function submitLogin(email, password) {
    // Show loading state
    const btn = document.querySelector('#loginForm button');
    btn.textContent = 'Logging in...';
    btn.disabled = true;

    fetch('/shop/includes/process_login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success then redirect to correct page
            document.getElementById('loginMessage').innerHTML =
                '<div class="alert alert-success">Login successful! Redirecting...</div>';

            // Redirect based on role
            setTimeout(() => {
                if (data.role === 'admin') {
                    window.location.href = '/shop/admin/dashboard.php';
                } else {
                    window.location.href = '/shop/index.php';
                }
            }, 1000);

        } else {
            document.getElementById('loginMessage').innerHTML =
                '<div class="alert alert-danger">' + data.message + '</div>';
            btn.textContent = 'Login';
            btn.disabled = false;
        }
    })
    .catch(error => {
        document.getElementById('loginMessage').innerHTML =
            '<div class="alert alert-danger">Something went wrong. Please try again.</div>';
        btn.textContent = 'Login';
        btn.disabled = false;
    });
}

// ========================================
// ATTACH EVENTS AFTER PAGE FULLY LOADS
// ========================================

document.addEventListener('DOMContentLoaded', function() {

    // Register button
    const registerBtn = document.getElementById('registerBtn');
    if (registerBtn) {
        registerBtn.addEventListener('click', function() {
            console.log('Register button clicked');
            validateRegister();
        });
    }

    // Login button
    const loginBtn = document.getElementById('loginBtn');
    if (loginBtn) {
        loginBtn.addEventListener('click', function() {
            console.log('Login button clicked');
            validateLogin();
        });
    }

});
