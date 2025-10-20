<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      background: #ffffff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 1rem;
    }

    .login-container {
      display: flex;
      box-shadow: 0 10px 50px rgba(0, 0, 0, 0.1);
      border-radius: 20px;
      overflow: hidden;
      max-width: 950px;
      width: 100%;
      min-height: 550px;
    }

    /* Left Info Panel */
    .login-info {
      background-color: #003249; /* Dark Teal */
      color: #ffffff;
      padding: 4rem 3rem;
      flex-basis: 50%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      border-top-right-radius: 120px;
      border-bottom-right-radius: 120px;
      position: relative;
      z-index: 1;
    }

    .info-header .plus-icon {
      font-size: 4rem;
      font-weight: 700;
      line-height: 1;
      margin-bottom: 1rem;
    }

    .info-header h1 {
      font-size: 1.7rem;
      font-weight: bold;
      letter-spacing: 1px;
      line-height: 1.3; /* Adjusted for two lines */
    }

    .info-header .welcome-text {
      margin-top: 0.5rem;
      font-size: 1rem;
      color: #e0e0e0;
    }
    
    .info-footer {
      font-size: 1rem;
      color: #e0e0e0;
      margin-top: 5rem;
    }

    /* Right Form Panel */
    .login-form-container {
      flex-basis: 50%;
      background: #ffffff;
      padding: 4rem 5rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .login-form-container h2 {
      font-weight: 700;
      font-size: 2rem;
      color: #003249;
      margin-bottom: 0.5rem;
    }
    
    .login-form-container .subtitle {
      color: #888;
      margin-bottom: 2.5rem;
    }

    .form-label {
      font-size: 0.8rem;
      font-weight: 600;
      color: #555;
      letter-spacing: 0.5px;
    }
    
    .input-group {
      border: 1px solid #ced4da;
      border-radius: 0.5rem;
      padding: 0 0.5rem;
    }

    .input-group .form-control {
      border: none;
      box-shadow: none;
    }
    
    .input-group .input-group-text {
      background-color: transparent;
      border: none;
    }
    
    .input-group:focus-within {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
    }

    .input-group i {
      color: #999;
    }
    
    #togglePassword {
        cursor: pointer;
    }
    
    input[type="password"]::-ms-reveal,
    input[type="password"]::-webkit-password-reveal-button {
        display: none;
        appearance: none;
    }

    .btn-login {
      background-color: #003249;
      border: none;
      color: white;
      font-weight: bold;
      padding: 0.8rem 0;
      width: 180px;
      border-radius: 10px;
      transition: background-color 0.3s ease;
    }

    .btn-login:hover {
      background-color: #004a6e;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .login-info {
            padding: 2rem;
            border-radius: 0;
            border-bottom-left-radius: 120px;
            border-bottom-right-radius: 120px;
        }
        .login-form-container {
            padding: 3rem 2rem;
        }
        .login-container {
            flex-direction: column;
            min-height: auto;
        }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <!-- Info Panel (Left Side) -->
    <div class="login-info">
      <div class="info-header">
        <div class="plus-icon">+</div>
        <!-- MODIFIED: Added a line break -->
        <h1>MACTAN VETERINARY<br>CLINC</h1>
        <p class="welcome-text">Welcome to VetSync!</p>
      </div>
      <p class="info-footer">Please login to access the system</p>
    </div>

    <!-- Form Panel (Right Side) -->
    <div class="login-form-container">
      <h2>LOGIN</h2>
      <p class="subtitle">Login using your registered credetials</p>

      <form method="POST" action="../utils/loginHandler.php">
        
        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger text-center">Invalid email or password.</div>
        <?php endif; ?>

        <div class="mb-3">
          <label for="user_un" class="form-label">USERNAME</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control" name="user_un" id="user_un" placeholder="username" required>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="user_pass" class="form-label">PASSWORD</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" name="user_pass" id="user_pass" placeholder="Password" required>
            <span class="input-group-text">
              <i class="bi bi-eye" id="togglePassword"></i>
            </span>
          </div>
        </div>
        
        <!-- MODIFIED: Added text-center class to center the button -->
        <div class="text-center">
            <button type="submit" class="btn btn-login">LOGIN</button>
        </div>
      </form>
    </div>
  </div>

  <!-- JAVASCRIPT for the eye icon -->
  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('user_pass');

    togglePassword.addEventListener('click', function () {
      // Toggle the type attribute
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      // Toggle the icon
      this.classList.toggle('bi-eye');
      this.classList.toggle('bi-eye-slash');
    });
  </script>

</body>
</html>