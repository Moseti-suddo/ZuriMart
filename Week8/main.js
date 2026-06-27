console.log('main.js loaded — version CHECK');

// ========================================
// PASSWORD STRENGTH CHECKER
// ========================================

function checkPasswordStrength(password) {
    let strength = 0;

    if (password.length >= 8)           strength++;
    if (/[A-Z]/.test(password))         strength++;
    if (/[0-9]/.test(password))         strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    const bar  = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');

    if (!bar || !text) return;

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
// HELPER FUNCTIONS — FORM ERRORS
// ========================================

function showError(inputId, errorId, message) {
    document.getElementById(inputId).classList.add('is-invalid');
    document.getElementById(errorId).textContent = message;
}

function clearError(inputId, errorId) {
    document.getElementById(inputId).classList.remove('is-invalid');
    document.getElementById(errorId).textContent = '';
}

// ========================================
// REGISTRATION FORM VALIDATION
// ========================================

function validateRegister() {
    const fullName        = document.getElementById('fullName').value.trim();
    const email           = document.getElementById('email').value.trim();
    const password        = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    let isValid = true;

    if (fullName === '') {
        showError('fullName', 'nameError', 'Full name is required');
        isValid = false;
    } else {
        clearError('fullName', 'nameError');
    }

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

    if (password === '') {
        showError('password', 'passwordError', 'Password is required');
        isValid = false;
    } else if (password.length < 8) {
        showError('password', 'passwordError', 'Password must be at least 8 characters');
        isValid = false;
    } else {
        clearError('password', 'passwordError');
    }

    if (confirmPassword === '') {
        showError('confirmPassword', 'confirmError', 'Please confirm your password');
        isValid = false;
    } else if (password !== confirmPassword) {
        showError('confirmPassword', 'confirmError', 'Passwords do not match');
        isValid = false;
    } else {
        clearError('confirmPassword', 'confirmError');
    }

    if (isValid) {
        submitRegistration(fullName, email, password);
    }
}

function submitRegistration(fullName, email, password) {
    const btn = document.querySelector('#registerForm button');
    btn.textContent = 'Creating account...';
    btn.disabled = true;

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
    .catch(() => {
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
    const email    = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;

    let isValid = true;

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

    if (password === '') {
        showError('loginPassword', 'loginPasswordError', 'Password is required');
        isValid = false;
    } else {
        clearError('loginPassword', 'loginPasswordError');
    }

    if (isValid) {
        submitLogin(email, password);
    }
}

function submitLogin(email, password) {
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
            document.getElementById('loginMessage').innerHTML =
                '<div class="alert alert-success">Login successful! Redirecting...</div>';
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            document.getElementById('loginMessage').innerHTML =
                '<div class="alert alert-danger">' + data.message + '</div>';
            btn.textContent = 'Login';
            btn.disabled = false;
        }
    })
    .catch(() => {
        document.getElementById('loginMessage').innerHTML =
            '<div class="alert alert-danger">Something went wrong. Please try again.</div>';
        btn.textContent = 'Login';
        btn.disabled = false;
    });
}

// ========================================
// CART — BADGE UPDATE
// ========================================

function updateCartBadge(count) {
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(badge => {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline' : 'none';
    });
}

// ========================================
// CART — ADD ITEM
// ========================================

function addToCart(productId) {
    const btn = document.querySelector(
        `.add-to-cart-btn[data-product-id="${productId}"]`
    );

    const originalText = btn.innerHTML;
    btn.innerHTML      = 'Adding...';
    btn.disabled       = true;

    fetch('/shop/includes/cart_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action:     'add',
            product_id: productId,
            quantity:   1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cart_count);

            btn.innerHTML = '✓ Added!';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
                btn.disabled = false;
            }, 2000);

        } else {
            btn.innerHTML = data.message;
            btn.classList.add('btn-danger');

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-danger');
                btn.disabled = false;
            }, 2000);

            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
        }
    })
    .catch(() => {
        btn.innerHTML = 'Error — try again';
        btn.disabled  = false;
    });
}

// ========================================
// CART — LOAD COUNT ON PAGE LOAD
// ========================================

function loadCartCount() {
    if (!document.querySelector('.cart-badge')) return;

    fetch('/shop/includes/cart_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'count' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) updateCartBadge(data.cart_count);
    })
    .catch(() => {});
}

// ========================================
// ATTACH EVENTS AFTER PAGE FULLY LOADS
// ========================================

document.addEventListener('DOMContentLoaded', function() {

    // Register button
    const registerBtn = document.getElementById('registerBtn');
    if (registerBtn) {
        registerBtn.addEventListener('click', function() {
            validateRegister();
        });
    }

    // Login button
    const loginBtn = document.getElementById('loginBtn');
    if (loginBtn) {
        loginBtn.addEventListener('click', function() {
            validateLogin();
        });
    }

        // Attach add to cart buttons — use once:true to prevent double firing
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.disabled) return;
            addToCart(parseInt(this.dataset.productId));
        });
    });
});
