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
            header("Location: " . BASE_PATH . "/admin/login");
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
            header("Location: " . BASE_PATH . "/admin");
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
                header("Location: " . BASE_PATH . "/admin/login");
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
                
                header("Location: " . BASE_PATH . "/admin");
                exit();
            } else {
                $_SESSION['login_error'] = "Invalid username or password.";
                header("Location: " . BASE_PATH . "/admin/login");
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
        header("Location: " . BASE_PATH . "/admin/login");
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

        // Calculate estimated revenue from completed services
        $revenue = 0;
        $stmt_all_completed = $this->db->prepare("SELECT service FROM appointments WHERE appointment_date = :selected_date AND status = 'Completed'");
        $stmt_all_completed->bindParam(':selected_date', $selected_date);
        $stmt_all_completed->execute();
        $completed_services = $stmt_all_completed->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($completed_services as $service) {
            if (preg_match('/\$([0-9]+)/', $service, $matches)) {
                $revenue += intval($matches[1]);
            }
        }

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

            $allowed_statuses = ['Confirmed', 'Completed', 'Cancelled'];
            if (!in_array($new_status, $allowed_statuses)) {
                die("Invalid status change attempt.");
            }

            $stmt = $this->db->prepare("UPDATE appointments SET status = :status WHERE id = :id");
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':id', $appointment_id);

            if ($stmt->execute()) {
                header("Location: " . BASE_PATH . "/admin?date=" . $redirect_date);
                exit();
            } else {
                echo "Error updating status.";
            }
        }
    }
}
?>