<?php
namespace Backend\Controllers;

use Backend\Db\Database;

class AdminController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Helper to enforce authentication
    private function requireAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            // Added leading backslash to force global scope
            header("Location: " . \BASE_PATH . "/admin/login");
            exit();
        }
    }

    // Render Admin Login Page
    public function loginView() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Redirect to dashboard if already logged in
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header("Location: " . \BASE_PATH . "/admin");
            exit();
        }

        // Generate CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require_once __DIR__ . '/../../frontend/views/admin_login.php';
    }

    // Handle Admin Authenticate Action
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die("Security Error: Invalid CSRF token.");
            }

            $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                $_SESSION['login_error'] = "Both fields are required.";
                header("Location: " . \BASE_PATH . "/admin/login");
                exit();
            }

            // Fetch admin from database
            $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $admin = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                // Regenerate session to prevent session fixation attacks
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                header("Location: " . \BASE_PATH . "/admin");
                exit();
            } else {
                $_SESSION['login_error'] = "Invalid username or password.";
                header("Location: " . \BASE_PATH . "/admin/login");
                exit();
            }
        }
    }

    // Handle Logout
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header("Location: " . \BASE_PATH . "/admin/login");
        exit();
    }

    // Render Dashboard
    public function dashboard() {
        $this->requireAuth();

        // Get selected date (Defaults to today)
        $selected_date = isset($_GET['date']) ? htmlspecialchars($_GET['date']) : date('Y-m-d');

        // 1. Fetch appointments for selected date
        $stmt = $this->db->prepare("SELECT * FROM appointments WHERE appointment_date = :selected_date ORDER BY appointment_time ASC");
        $stmt->bindParam(':selected_date', $selected_date);
        $stmt->execute();
        $appointments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 2. Fetch system-wide summary metrics for selected date
        $stmt_metrics = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM appointments 
            WHERE appointment_date = :selected_date
        ");
        $stmt_metrics->bindParam(':selected_date', $selected_date);
        $stmt_metrics->execute();
        $metrics = $stmt_metrics->fetch(\PDO::FETCH_ASSOC);

        // 3. Calculate daily revenue directly from database based on actual stored prices
        $stmt_rev = $this->db->prepare("SELECT SUM(price_paid) as total_rev FROM appointments WHERE appointment_date = :selected_date AND status = 'Completed'");
        $stmt_rev->bindParam(':selected_date', $selected_date);
        $stmt_rev->execute();
        $revenue = floatval($stmt_rev->fetch(\PDO::FETCH_ASSOC)['total_rev'] ?? 0);

        require_once __DIR__ . '/../../frontend/views/admin_dashboard.php';
    }

    // Handle Appointment Status Updates (Confirmed, Completed, Cancelled)
    public function updateStatus() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die("Security Error: Invalid CSRF token.");
            }

            $appointment_id = intval($_POST['appointment_id']);
            $new_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
            $redirect_date = filter_input(INPUT_POST, 'redirect_date', FILTER_SANITIZE_SPECIAL_CHARS);
            
            // Get the price paid submitted from the modal form
            $price_paid = isset($_POST['price_paid']) ? floatval($_POST['price_paid']) : 0.00;

            $allowed_statuses = ['Confirmed', 'Completed', 'Cancelled'];
            if (!in_array($new_status, $allowed_statuses)) {
                die("Invalid status change attempt.");
            }

            // Save both the new status and the custom manual price entry in the DB
            $stmt = $this->db->prepare("UPDATE appointments SET status = :status, price_paid = :price_paid WHERE id = :id");
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':price_paid', $price_paid);
            $stmt->bindParam(':id', $appointment_id);

            if ($stmt->execute()) {
                header("Location: " . \BASE_PATH . "/admin?date=" . $redirect_date);
                exit();
            } else {
                echo "Error updating status.";
            }
        }
    }

    // Render Admin Registration Page
    public function registerView() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Generate CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // --- CHECK ADMIN LIMIT ---
        $stmt_count = $this->db->query("SELECT COUNT(*) FROM admins");
        $admin_count = $stmt_count->fetchColumn();
        $limit_reached = ($admin_count >= 2);
        // -------------------------

        require_once __DIR__ . '/../../frontend/views/admin_register.php';
    }

    // Handle Admin Registration Action
    public function register() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // --- CHECK ADMIN LIMIT BEFORE SAVING ---
            $stmt_count = $this->db->query("SELECT COUNT(*) FROM admins");
            $admin_count = $stmt_count->fetchColumn();

            if ($admin_count >= 2) {
                $_SESSION['register_error'] = "Registration locked: Maximum of 2 admin accounts allowed.";
                header("Location: " . \BASE_PATH . "/admin/register");
                exit();
            }
            // ---------------------------------------

            $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                $_SESSION['register_error'] = "Username and password are required.";
                header("Location: " . \BASE_PATH . "/admin/register");
                exit();
            }

            // Check if username already exists
            $stmt_check = $this->db->prepare("SELECT id FROM admins WHERE username = :username");
            $stmt_check->bindParam(':username', $username);
            $stmt_check->execute();
            if ($stmt_check->fetch()) {
                $_SESSION['register_error'] = "Username is already taken.";
                header("Location: " . \BASE_PATH . "/admin/register");
                exit();
            }

            // Hash the password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into database
            $stmt = $this->db->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['register_success'] = "Admin created successfully! You can now log in.";
                header("Location: " . \BASE_PATH . "/admin/register");
                exit();
            } else {
                $_SESSION['register_error'] = "Failed to create admin.";
                header("Location: " . \BASE_PATH . "/admin/register");
                exit();
            }
        }
    }
}
?>