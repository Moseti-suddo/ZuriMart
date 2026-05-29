<?php
$pageTitle = 'Register | ZuriMart';
require_once 'includes/header.php';
?>

<style>
  .auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(ellipse at center, #1a0533 0%, #0f0f13 70%);
    padding: 2rem 1rem;
  }

  .auth-card {
    width: 100%;
    max-width: 480px;
    background: #1a1a2e;
    border: 1px solid #2d2d44;
    border-radius: 16px;
    overflow: hidden;
  }

  .auth-banner {
    background: linear-gradient(135deg, #1a0533 0%, #2d1b69 50%, #1a0533 100%);
    padding: 2rem;
    text-align: center;
    border-bottom: 1px solid #2d2d44;
    position: relative;
    overflow: hidden;
  }

  .auth-banner::before {
    content: '';
    position: absolute;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(124,58,237,0.3) 0%, transparent 70%);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
  }

  .auth-banner h2 {
    font-size: 1.8rem;
    font-weight: 800;
    color: white;
    margin-bottom: 0.3rem;
    position: relative;
  }

  .auth-banner h2 span { color: #f59e0b; }

  .auth-banner p {
    color: #94a3b8;
    font-size: 0.9rem;
    margin: 0;
    position: relative;
  }

  .auth-banner .banner-icon {
    font-size: 3rem;
    margin-bottom: 0.8rem;
    position: relative;
  }

  .auth-body { padding: 2rem; }

  .auth-body h4 {
    font-weight: 700;
    color: white;
    margin-bottom: 0.3rem;
  }

  .auth-body .subtitle {
    color: #94a3b8;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
  }

  .auth-divider {
    border-color: #2d2d44;
    margin: 1.2rem 0;
  }

  .auth-footer-text {
    text-align: center;
    color: #94a3b8;
    font-size: 0.9rem;
    margin-top: 1.2rem;
  }

  .auth-footer-text a {
    color: #7c3aed;
    text-decoration: none;
    font-weight: 600;
  }

  .auth-footer-text a:hover { color: #f59e0b; }
</style>

<div class="auth-page">
  <div class="auth-card">

    <!-- TOP BANNER -->
    <div class="auth-banner">
      <div class="banner-icon">🛍️</div>
      <h2>Zuri<span>Mart</span></h2>
      <p>Create your account to start shopping</p>
    </div>

    <!-- FORM AREA -->
    <div class="auth-body">
      <h4>Create Account</h4>
      <p class="subtitle">Join thousands of shoppers on ZuriMart</p>

      <div id="formMessage"></div>

      <form id="registerForm">

        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" class="form-control" id="fullName"
                 placeholder="Enter your full name">
          <div class="invalid-feedback" id="nameError"></div>
        </div>

        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" class="form-control" id="email"
                 placeholder="Enter your email">
          <div class="invalid-feedback" id="emailError"></div>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" id="password"
                 placeholder="Create a password"
                 oninput="checkPasswordStrength(this.value)">
          <div class="invalid-feedback" id="passwordError"></div>
          <div class="progress mt-2" style="height: 5px; background: #2d2d44;">
            <div class="progress-bar" id="strengthBar" style="width: 0%"></div>
          </div>
          <small id="strengthText" class="text-muted"></small>
        </div>

        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="confirmPassword"
                 placeholder="Repeat your password">
          <div class="invalid-feedback" id="confirmError"></div>
        </div>

        <button type="button" class="btn btn-primary w-100 py-2"
                id="registerBtn" onclick="validateRegister()">
          Create Account
        </button>

      </form>

      <hr class="auth-divider">

      <p class="auth-footer-text">
        Already have an account? <a href="login.php">Login here</a>
      </p>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/shop/assets/js/main.js"></script>
</body>
</html>