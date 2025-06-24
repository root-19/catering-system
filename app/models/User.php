<?php

namespace root_dev\Models;

require_once __DIR__ . '/../../config/database.php';
use root_dev\Config\Database;

class User {
    private $db;
    private $errors = [];
    protected $table = 'users';
    protected $fillable = ['username', 'email', 'phone', 'password', 'role', 'api_key', 'remember_token'];

    public function __construct() {
        $this->db = \Database::connect();
    }

    // Get all users
    public function all() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Find user by id
    public function find($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Create a new user
    public function create($data) {
        try {
            $columns = implode(', ', $this->fillable);
            $placeholders = ':' . implode(', :', $this->fillable);
            $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->db->prepare($query);
            return $stmt->execute($data);
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Update user by id
    public function update($id, $data) {
        try {
            $set = '';
            foreach ($this->fillable as $column) {
                $set .= "{$column} = :{$column}, ";
            }
            $set = rtrim($set, ', ');
            $query = "UPDATE {$this->table} SET {$set} WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $data['id'] = $id;
            return $stmt->execute($data);
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Delete user by id
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Check if email exists
    public function emailExists($email) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Get user data by email
    public function getUserByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Get user by remember token
    public function getUserByRememberToken($token) {
        try {
            $query = "SELECT * FROM users WHERE remember_token = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$token]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Update remember token
    public function updateRememberToken($userId, $token) {
        try {
            $query = "UPDATE users SET remember_token = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$token, $userId]);
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Clear remember token
    public function clearRememberToken($userId) {
        try {
            $query = "UPDATE users SET remember_token = NULL WHERE id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$userId]);
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Check if phone exists
    public function phoneExists($phone) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE phone = ?");
            $stmt->execute([$phone]);
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Generate API key
    private function generateApiKey() {
        $random = bin2hex(random_bytes(5)); // 10 characters
        return 'key_' . $random;
    }

    // Check if API key exists
    public function apiKeyExists($apiKey) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE api_key = ?");
            $stmt->execute([$apiKey]);
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Get user by API key
    public function getUserByApiKey($apiKey) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE api_key = ?");
            $stmt->execute([$apiKey]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Generate new API key for user
    public function generateNewApiKey($userId) {
        try {
            do {
                $newApiKey = $this->generateApiKey();
            } while ($this->apiKeyExists($newApiKey));

            $stmt = $this->db->prepare("UPDATE users SET api_key = ? WHERE id = ?");
            return $stmt->execute([$newApiKey, $userId]) ? $newApiKey : false;
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Register a new user
    public function register($username, $email, $phone, $password, $role = 'user') {
        try {
            // Validate input
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = "Invalid email format";
                return false;
            }

            if (strlen($username) < 3 || strlen($username) > 20) {
                $this->errors[] = "Username must be between 3 and 20 characters";
                return false;
            }

            if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $this->errors[] = "Username can only contain letters, numbers, and underscores";
                return false;
            }

            if (!preg_match('/^[0-9]{11}$/', $phone)) {
                $this->errors[] = "Phone number must be 11 digits";
                return false;
            }

            if (strlen($password) < 8) {
                $this->errors[] = "Password must be at least 8 characters long";
                return false;
            }

            // Check if email already exists
            if ($this->emailExists($email)) {
                $this->errors[] = "Email already exists";
                return false;
            }

            // Check if phone already exists
            if ($this->phoneExists($phone)) {
                $this->errors[] = "Phone number already exists";
                return false;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Generate API key
            do {
                $apiKey = $this->generateApiKey();
            } while ($this->apiKeyExists($apiKey));
            
            $stmt = $this->db->prepare("INSERT INTO users (username, email, phone, password, role, api_key, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $result = $stmt->execute([$username, $email, $phone, $hashedPassword, $role, $apiKey]);
            
            if (!$result) {
                $this->errors[] = "Failed to register user";
                return false;
            }
            
            return $apiKey; // Return the generated API key
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Update password
    public function updatePasswordByEmail($email, $newPassword) {
        try {
            if (strlen($newPassword) < 8) {
                $this->errors[] = "Password must be at least 8 characters long";
                return false;
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE email = ?");
            return $stmt->execute([$hashedPassword, $email]);
        } catch (\PDOException $e) {
            $this->errors[] = "Database error: " . $e->getMessage();
            return false;
        }
    }

    // Get last error
    public function getErrors() {
        return $this->errors;
    }
}
