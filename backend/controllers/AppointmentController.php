<?php
namespace Backend\Controllers;

use Backend\Db\Database;

class AppointmentController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Render the homepage with the list of appointments
    public function index() {
        // Generate a secure CSRF token if one doesn't exist
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Fetch upcoming appointments ordered by date and time
        $stmt = $this->db->prepare("SELECT * FROM appointments ORDER BY appointment_date ASC, appointment_time ASC");
        $stmt->execute();
        $appointments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Load the view (passing the $appointments variable to it)
        require_once __DIR__ . '/../../frontend/views/index.php';
    }

    // Handle the form submission to book an appointment
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Verify CSRF Token
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die("Security Error: Invalid CSRF token.");
            }

            // 2. Strict Input Sanitization and Validation
            $name = trim(filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_SPECIAL_CHARS));
            $phone = preg_replace('/[^0-9+\-\(\)\s]/', '', $_POST['phone']); // Allow only valid phone characters
            $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_SPECIAL_CHARS);
            $date = $_POST['appointment_date'];
            $time = $_POST['appointment_time'];

            // Validate that required fields aren't empty
            if (empty($name) || empty($phone) || empty($service) || empty($date) || empty($time)) {
                die("Validation Error: All fields are required.");
            }

            $query = "INSERT INTO appointments (customer_name, phone, service, appointment_date, appointment_time) 
                      VALUES (:name, :phone, :service, :date, :time)";
            
            $stmt = $this->db->prepare($query);

            // Bind parameters
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':service', $service);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $time);

            if ($stmt->execute()) {
                // Redirect back to home after successful booking
                header("Location: " . dirname($_SERVER['SCRIPT_NAME']));
                exit();
            } else {
                echo "Error booking appointment.";
            }
        }
    }
}
?>