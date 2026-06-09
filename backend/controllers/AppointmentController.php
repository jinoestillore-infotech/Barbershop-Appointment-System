<?php
namespace Backend\Controllers;

use Backend\Db\Database;

class AppointmentController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Render the customer view with interactive time-slot cards
    public function index() {
        // Generate a secure CSRF token if one doesn't exist
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Get selected date (Default to today)
        $selected_date = isset($_GET['date']) ? htmlspecialchars($_GET['date']) : date('Y-m-d');

        // Define our fixed sets of time slots
        $time_slots = [
            ['start' => '07:00:00', 'end' => '07:30:00', 'label' => '7:00 - 7:30 AM'],
            ['start' => '07:35:00', 'end' => '07:55:00', 'label' => '7:35 - 7:55 AM'],
            ['start' => '08:00:00', 'end' => '08:25:00', 'label' => '8:00 - 8:25 AM'],
            ['start' => '08:30:00', 'end' => '09:00:00', 'label' => '8:30 - 9:00 AM'],
            ['start' => '09:05:00', 'end' => '09:30:00', 'label' => '9:05 - 9:30 AM'],
            ['start' => '09:35:00', 'end' => '10:00:00', 'label' => '9:35 - 10:00 AM'],
            ['start' => '10:05:00', 'end' => '10:30:00', 'label' => '10:05 - 10:30 AM'],
            ['start' => '10:35:00', 'end' => '11:00:00', 'label' => '10:35 - 11:00 AM'],
            ['start' => '13:00:00', 'end' => '13:30:00', 'label' => '1:00 - 1:30 PM'],
            ['start' => '13:35:00', 'end' => '13:55:00', 'label' => '1:35 - 1:55 PM'],
            ['start' => '14:00:00', 'end' => '14:30:00', 'label' => '2:00 - 2:30 PM'],
            ['start' => '14:35:00', 'end' => '15:00:00', 'label' => '2:35 - 3:00 PM'],
            ['start' => '15:05:00', 'end' => '15:30:00', 'label' => '3:05 - 3:30 PM'],
            ['start' => '15:35:00', 'end' => '16:00:00', 'label' => '3:35 - 4:00 PM']
        ];

        // Fetch already booked times for the selected date
        $stmt = $this->db->prepare("SELECT appointment_time FROM appointments WHERE appointment_date = :selected_date");
        $stmt->bindParam(':selected_date', $selected_date);
        $stmt->execute();
        $booked_rows = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Map booked times to simplify availability checking
        // (Trimming seconds because MySQL TIME returns HH:MM:SS)
        $booked_times = array_map(function($time) {
            return substr($time, 0, 5);
        }, $booked_rows);

        // Load the view file
        require_once __DIR__ . '/../../frontend/views/index.php';
    }

    // Securely handle form submission
    public function store() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF Token
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die("Security Error: Invalid CSRF token.");
            }

            // Clean & Sanitize
            $name = trim(filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_SPECIAL_CHARS));
            $phone = preg_replace('/[^0-9+\-\(\)\s]/', '', $_POST['phone']);
            $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_SPECIAL_CHARS);
            $date = htmlspecialchars($_POST['appointment_date']);
            $time = htmlspecialchars($_POST['appointment_time']); // e.g. "07:00:00" or "07:00"
            $time_label = htmlspecialchars($_POST['appointment_time_label']);

            if (empty($name) || empty($phone) || empty($service) || empty($date) || empty($time)) {
                die("Validation Error: All fields are required.");
            }

            // Double check if the slot is already booked (Prevents race conditions)
            $check_stmt = $this->db->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = :date AND appointment_time = :time");
            $check_stmt->bindParam(':date', $date);
            $check_stmt->bindParam(':time', $time);
            $check_stmt->execute();
            if ($check_stmt->fetchColumn() > 0) {
                die("This time slot is already booked! Please select another slot.");
            }

            // Perform Save
            $query = "INSERT INTO appointments (customer_name, phone, service, appointment_date, appointment_time) 
                      VALUES (:name, :phone, :service, :date, :time)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':service', $service);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $time);

            if ($stmt->execute()) {
                // Flash details to session to render the popup modal on redirect
                $_SESSION['booking_success'] = [
                    'name' => $name,
                    'service' => $service,
                    'date' => date('F d, Y', strtotime($date)),
                    'time_label' => $time_label
                ];
                
                // Redirect back keeping the selected date
                header("Location: " . dirname($_SERVER['SCRIPT_NAME']) . "/?date=" . $date);
                exit();
            } else {
                echo "Error saving appointment.";
            }
        }
    }
}
?>