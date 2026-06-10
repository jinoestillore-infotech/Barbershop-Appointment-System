<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .register-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        .alert { padding: 10px; background-color: #f8d7da; color: #721c24; margin-bottom: 15px; border-radius: 4px; }
        .success { padding: 10px; background-color: #d4edda; color: #155724; margin-bottom: 15px; border-radius: 4px; }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Create Admin</h2>
    
    <?php if (isset($_SESSION['register_error'])): ?>
        <div class="alert"><?php echo $_SESSION['register_error']; unset($_SESSION['register_error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['register_success'])): ?>
        <div class="success"><?php echo $_SESSION['register_success']; unset($_SESSION['register_success']); ?></div>
    <?php endif; ?>

    <?php if (!isset($limit_reached) || !$limit_reached): ?>
        <form action="<?php echo \BASE_PATH; ?>/admin/register" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Register Admin</button>
        </form>
    <?php else: ?>
        <div class="alert" style="text-align: center; background-color: #e2e3e5; color: #383d41;">
            <strong>Registration Closed</strong><br>
            The maximum number of allowed admins (2) has been reached.
        </div>
    <?php endif; ?>
    
    <p style="text-align: center; margin-top: 15px;">
        <a href="<?php echo \BASE_PATH; ?>/admin/login">Back to Login</a>
    </p>
</div>

</body>
</html>