<?php
// session_start();
require_once __DIR__ . '/../vendor/autoload.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {      
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password strength
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $error = "Password must contain at least one uppercase letter";
    } elseif (!preg_match("/[a-z]/", $password)) {
        $error = "Password must contain at least one lowercase letter";
    } elseif (!preg_match("/[0-9]/", $password)) {
        $error = "Password must contain at least one number";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // TODO: Save user to database
        // After successful registration, generate JWT token
        $token = \App\Helpers\JwtHelper::generateToken([
            'user_id' => $user_id, // Replace with actual user ID
            'username' => $username
        ]);
        
        // Set token in cookie
        setcookie('auth_token', $token, time() + 3600, '/', '', true, true);
        
        // Redirect to dashboard
        header('Location: /dashboard');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - SMS Connect</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    .gradient-bg {
      background: linear-gradient(135deg, #fde047 0%, #000 100%); /* yellow to black */
    }
    .hover-scale {
      transition: transform 0.3s ease;
    }
    .hover-scale:hover {
      transform: scale(1.05);
    }
    .password-container {
      position: relative;
    }
    .password-toggle {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6B7280;
    }
    .password-toggle:hover {
      color: #000;
    }
    .error-message {
      color: #dc2626;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }
    .input-error {
      border-color: #dc2626 !important;
    }
    .input-error:focus {
      border-color: #dc2626 !important;
      ring-color: #dc2626 !important;
    }
  </style>
</head>
<body class="bg-white text-black">

  <!-- Register Form Section -->
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 gradient-bg">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl" data-aos="fade-up">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-black">Create your account</h2>
        <p class="mt-2 text-center text-sm text-gray-700">
          Sign up for Reservation Catering and manage your bookings with ease
        </p>
      </div>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['error']); ?></span>
          <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <form action="/register" method="POST" class="mt-8 space-y-6" id="registerForm">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="rounded-md shadow-sm space-y-4">
          <div>
            <label for="username" class="block text-sm font-medium text-black">Username</label>
            <input type="text" name="username" id="username" required 
                   pattern="[a-zA-Z0-9_]{3,20}"
                   title="Username must be between 3 and 20 characters and can only contain letters, numbers, and underscores"
                   class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-black focus:outline-none focus:ring-yellow-400 focus:border-yellow-500 focus:z-10 sm:text-sm bg-white" />
            <div class="error-message" id="username-error"></div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-black">Email address</label>
            <input type="email" name="email" id="email" required 
                   class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-black focus:outline-none focus:ring-yellow-400 focus:border-yellow-500 focus:z-10 sm:text-sm bg-white" />
            <div class="error-message" id="email-error"></div>
          </div>

          <div>
            <label for="phone" class="block text-sm font-medium text-black">Phone Number</label>
            <input type="tel" name="phone" id="phone" required 
                   pattern="[0-9]{11}"
                   title="Please enter a valid 11-digit phone number"
                   class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-black focus:outline-none focus:ring-yellow-400 focus:border-yellow-500 focus:z-10 sm:text-sm bg-white" />
            <div class="error-message" id="phone-error"></div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-black">Password</label>
            <div class="password-container">
              <input type="password" name="password" id="password" required 
                     minlength="8"
                     class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-black focus:outline-none focus:ring-yellow-400 focus:border-yellow-500 focus:z-10 sm:text-sm bg-white" />
              <span class="password-toggle" onclick="togglePassword('password')">
                <i class="fas fa-eye"></i>
              </span>
            </div>
            <div class="error-message" id="password-error"></div>
          </div>

          <div>
            <label for="confirm_password" class="block text-sm font-medium text-black">Confirm Password</label>
            <div class="password-container">
              <input type="password" name="confirm_password" id="confirm_password" required 
                     minlength="8"
                     class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-black focus:outline-none focus:ring-yellow-400 focus:border-yellow-500 focus:z-10 sm:text-sm bg-white" />
              <span class="password-toggle" onclick="togglePassword('confirm_password')">
                <i class="fas fa-eye"></i>
              </span>
            </div>
            <div class="error-message" id="confirm-password-error"></div>
          </div>
        </div>

        <div>
          <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-black bg-yellow-400 hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-300">
            Register
          </button>
        </div>

        <div class="text-center">
          <p class="text-sm text-gray-700">
            Already have an account? 
            <a href="/login" class="font-medium text-yellow-600 hover:text-yellow-500">
              Login here
            </a>
          </p>
        </div>
      </form>
    </div>
  </div>

  <script>
    AOS.init();

    function togglePassword(inputId) {
      const passwordInput = document.getElementById(inputId);
      const icon = passwordInput.nextElementSibling.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }

    // Form validation
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      let isValid = true;
      const errors = {};

      // Username validation
      const username = document.getElementById('username');
      if (!username.value.match(/^[a-zA-Z0-9_]{3,20}$/)) {
        errors.username = 'Username must be between 3 and 20 characters and can only contain letters, numbers, and underscores';
        username.classList.add('input-error');
        isValid = false;
      } else {
        username.classList.remove('input-error');
      }

      // Email validation
      const email = document.getElementById('email');
      if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        errors.email = 'Please enter a valid email address';
        email.classList.add('input-error');
        isValid = false;
      } else {
        email.classList.remove('input-error');
      }

      // Phone validation
      const phone = document.getElementById('phone');
      if (!phone.value.match(/^[0-9]{11}$/)) {
        errors.phone = 'Please enter a valid 11-digit phone number';
        phone.classList.add('input-error');
        isValid = false;
      } else {
        phone.classList.remove('input-error');
      }

      // Password validation
      const password = document.getElementById('password');
      if (password.value.length < 8) {
        errors.password = 'Password must be at least 8 characters long';
        password.classList.add('input-error');
        isValid = false;
      } else if (!password.value.match(/[A-Z]/)) {
        errors.password = 'Password must contain at least one uppercase letter';
        password.classList.add('input-error');
        isValid = false;
      } else if (!password.value.match(/[a-z]/)) {
        errors.password = 'Password must contain at least one lowercase letter';
        password.classList.add('input-error');
        isValid = false;
      } else if (!password.value.match(/[0-9]/)) {
        errors.password = 'Password must contain at least one number';
        password.classList.add('input-error');
        isValid = false;
      } else {
        password.classList.remove('input-error');
      }

      // Confirm password validation
      const confirmPassword = document.getElementById('confirm_password');
      if (password.value !== confirmPassword.value) {
        errors.confirmPassword = 'Passwords do not match';
        confirmPassword.classList.add('input-error');
        isValid = false;
      } else {
        confirmPassword.classList.remove('input-error');
      }

      // Display errors
      document.getElementById('username-error').textContent = errors.username || '';
      document.getElementById('email-error').textContent = errors.email || '';
      document.getElementById('phone-error').textContent = errors.phone || '';
      document.getElementById('password-error').textContent = errors.password || '';
      document.getElementById('confirm-password-error').textContent = errors.confirmPassword || '';

      if (!isValid) {
        e.preventDefault();
      }
    });
  </script>
</body>
</html>

