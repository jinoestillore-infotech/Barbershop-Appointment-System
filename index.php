<?php
// Enable error reporting for debugging (Disable in Production!)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session for CSRF token management
session_start();

// Define a constant to protect sensitive included files
define('SECURE_ACCESS', true);

// Require essential files
require_once __DIR__ . '/backend/db/Database.php';
require_once __DIR__ . '/backend/routes/Router.php';
require_once __DIR__ . '/backend/controllers/AppointmentController.php';

use Backend\Routes\Router;
use Backend\Controllers\AppointmentController;

// Initialize the Router
$router = new Router();

// Define Application Routes
$router->get('/', [AppointmentController::class, 'index']);
$router->post('/book', [AppointmentController::class, 'store']);
$router->get('/admin/login', [AdminController::class, 'loginView']);
$router->post('/admin/login', [AdminController::class, 'login']);
$router->post('/admin/logout', [AdminController::class, 'logout']);
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->post('/admin/update-status', [AdminController::class, 'updateStatus']);

// Dispatch the request
$router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
?>