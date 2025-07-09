<?php
namespace root_dev\Controller;

require_once __DIR__ . '/../models/User.php'; 
require_once __DIR__ . '/../auth/UserAuth.php';
require_once __DIR__ . '/../auth/AdminAuth.php';
require_once __DIR__ . '/../../config/database.php';

use root_dev\Models\User;
use root_dev\Auth\UserAuth;
use root_dev\Auth\AdminAuth;
use root_dev\Config\Database;

class AuthController {
    private $db;
    private $userAuth;
    private $adminAuth;

    private function sanitizeInput($input) {
        if (is_string($input)) {
            return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
        }
        return $input;
    }

    private function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function validatePassword($password) {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        return strlen($password) >= 8 && 
               preg_match('/[A-Z]/', $password) && 
               preg_match('/[a-z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = \Database::connect();
        $this->userAuth = new UserAuth();
        $this->adminAuth = new AdminAuth();
    }

    public function index() {
        header('Location: /');
        exit();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Please fill in all fields';
                header('Location: /login');
                exit();
            }

            if (!$this->validateEmail($email)) {
                $_SESSION['error'] = 'Please enter a valid email address';
                header('Location: /login');
                exit();
            }

            // Rate limiting check
            if ($this->isLoginAttemptLimited($email)) {
                $_SESSION['error'] = 'Too many login attempts. Please try again later.';
                header('Location: /login');
                exit();
            }

            // 1. Check admins
            $adminStmt = $this->db->prepare('SELECT * FROM admins WHERE email = ?');
            $adminStmt->execute([$email]);
            $admin = $adminStmt->fetch(\PDO::FETCH_ASSOC);
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['role'] = 'admin';
                $this->resetLoginAttempts($email);
                header('Location: /admin/dashboard');
                exit();
            }

            // 2. Check users
            $userStmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
            $userStmt->execute([$email]);
            $user = $userStmt->fetch(\PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 'user';
                $this->resetLoginAttempts($email);
                header('Location: /dashboard');
                exit();
            }

            // 3. Check helpers
            $helperStmt = $this->db->prepare('SELECT * FROM helpers WHERE email = ?');
            $helperStmt->execute([$email]);
            $helper = $helperStmt->fetch(\PDO::FETCH_ASSOC);
            if ($helper && password_verify($password, $helper['password'])) {
                $_SESSION['user_id'] = $helper['id'];
                $_SESSION['username'] = $helper['username'];
                $_SESSION['role'] = 'helper';
                $this->resetLoginAttempts($email);
                header('Location: /helper/dashboard');
                exit();
            }

            // If none matched
            $this->incrementLoginAttempts($email);
            $_SESSION['error'] = 'Invalid email or password';
            header('Location: /login');
            exit();
        }

        // Check for remember token cookie
        $rememberToken = $_COOKIE['remember_token'] ?? null;
        if ($rememberToken) {
            // Try admin remember token
            $adminUser = $this->adminAuth->getUserByRememberToken($rememberToken);
            if ($adminUser) {
                $_SESSION['user_id'] = $adminUser['id'];
                $_SESSION['username'] = $adminUser['username'];
                $_SESSION['role'] = 'admin';
                header('Location: /admin/dashboard');
                exit();
            }

            // Try user remember token
            $user = $this->userAuth->getUserByRememberToken($rememberToken);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 'user';
                header('Location: /dashboard');
                exit();
            }

            // Invalid remember token, clear it
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }

        require_once __DIR__ . '/../../public/login.php';
    }

    private function setUserSession($user, $role) {
        if (!is_array($user)) {
            throw new \Exception('Invalid user data provided');
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $role;
        $_SESSION['api_key'] = $user['api_key'] ?? null;
        $_SESSION['phone'] = $user['phone'] ?? null;
        // Set allowed_sections for admin
        if ($role === 'admin' && isset($user['allowed_sections'])) {
            $_SESSION['admin_allowed_sections'] = $user['allowed_sections'];
        } else {
            unset($_SESSION['admin_allowed_sections']);
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->sanitizeInput(trim($_POST['username'] ?? ''));
            $email = $this->sanitizeInput(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validate all required fields
            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                $_SESSION['error'] = "All fields are required.";
                header('Location: /register');
                exit();
            }

            // Validate email format
            if (!$this->validateEmail($email)) {
                $_SESSION['error'] = "Please enter a valid email address.";
                header('Location: /register');
                exit();
            }

            // Validate password strength
            if (!$this->validatePassword($password)) {
                $_SESSION['error'] = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.";
                header('Location: /register');
                exit();
            }

            // Validate password confirmation
            if ($password !== $confirmPassword) {
                $_SESSION['error'] = "Passwords do not match.";
                header('Location: /register');
                exit();
            }

            // Check if email already exists
            if ($this->userAuth->emailExists($email) || $this->adminAuth->emailExists($email)) {
                $_SESSION['error'] = "Email is already registered.";
                header('Location: /register');
                exit();
            }

            // Validate username length and format
            if (strlen($username) < 3 || strlen($username) > 50 || !preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
                $_SESSION['error'] = "Username must be between 3 and 50 characters and can only contain letters, numbers, underscores, and hyphens.";
                header('Location: /register');
                exit();
            }

            // Validate phone number
            if (!isset($_POST['phone']) || !preg_match('/^[0-9]{11}$/', $_POST['phone'])) {
                $_SESSION['error'] = "Please enter a valid 11-digit phone number.";
                header('Location: /register');
                exit();
            }

            $phone = $_POST['phone'];

            try {
                $user = new User();
                $result = $user->register($username, $email, $phone, $password, 'user');
                
                if ($result !== false) {
                    $userData = $user->getUserByEmail($email);
                    if ($userData) {
                        $this->setUserSession($userData, 'user');
                        header('Location: /dashboard');
                        exit();
                    }
                }
                
                // If we get here, something went wrong
                $errors = $user->getErrors();
                $_SESSION['error'] = !empty($errors) ? implode(", ", $errors) : "Registration failed. Please try again.";
                header('Location: /register');
                exit();
            } catch (\Exception $e) {
                error_log("Registration error: " . $e->getMessage());
                $_SESSION['error'] = "An error occurred during registration. Please try again later.";
                header('Location: /register');
                exit();
            }
        }

        require_once __DIR__ . '/../../public/register.php';
    }

    public function logout() {
        // Get user info before clearing session
        $userId = $_SESSION['user_id'] ?? null;
        $role = $_SESSION['role'] ?? null;

        // Clear remember token if exists
        if (isset($_COOKIE['remember_token'])) {
            // Clear the cookie
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
            
            // Clear token from database based on role
            if ($userId) {
                if ($role === 'admin') {
                    $this->adminAuth->clearRememberToken($userId);
                } else {
                    $this->userAuth->clearRememberToken($userId);
                }
            }
        }

        // Clear session
        session_unset();
        session_destroy();

        // Redirect to login page
        header('Location: /index');
        exit();
    }

    public function forgetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if (empty($email)) {
                $_SESSION['error'] = "Email is required.";
                header('Location: /forget-password');
                exit();
            }

            // Check if email exists in either user or admin tables
            $user = new User();
            if (!$user->emailExists($email)) {
                $_SESSION['error'] = "Email not found.";
                header('Location: /forget-password');
                exit();
            }

            // Generate password reset token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            try {
                $stmt = $this->db->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
                $stmt->execute([$token, $expiry, $email]);

                // TODO: Send password reset email
                // For now, just store the token in session for demo purposes
                $_SESSION['reset_token'] = $token;
                $_SESSION['success'] = "Password reset instructions have been sent to your email.";
                header('Location: /login');
                exit();

            } catch (\PDOException $e) {
                $_SESSION['error'] = "An error occurred. Please try again later.";
                header('Location: /forget-password');
                exit();
            }
        }

        require_once __DIR__ . '/../../public/forget-password.php';
    }

    private function isLoginAttemptLimited($email) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as attempts, MAX(attempt_time) as last_attempt 
                                       FROM login_attempts 
                                       WHERE email = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
            $stmt->execute([$email]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $result['attempts'] >= 5;
        } catch (\PDOException $e) {
            // If table doesn't exist, create it
            $this->createLoginAttemptsTable();
            return false;
        }
    }

    private function incrementLoginAttempts($email) {
        try {
            $stmt = $this->db->prepare("INSERT INTO login_attempts (email, attempt_time) VALUES (?, NOW())");
            $stmt->execute([$email]);
        } catch (\PDOException $e) {
            // If table doesn't exist, create it and try again
            $this->createLoginAttemptsTable();
            $stmt = $this->db->prepare("INSERT INTO login_attempts (email, attempt_time) VALUES (?, NOW())");
            $stmt->execute([$email]);
        }
    }

    private function resetLoginAttempts($email) {
        $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE email = ?");
        $stmt->execute([$email]);
    }

    private function createLoginAttemptsTable() {
        $this->db->exec("CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            attempt_time DATETIME NOT NULL,
            INDEX (email, attempt_time)
        )");
    }

    private function rotateRememberToken($userId, $role = 'user') {
        $token = bin2hex(random_bytes(32));
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        
        if ($role === 'admin') {
            $this->adminAuth->updateRememberToken($userId, $hashedToken);
        } else {
            $this->userAuth->updateRememberToken($userId, $hashedToken);
        }
        
        setcookie('remember_token', $token, [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        return $token;
    }
}   