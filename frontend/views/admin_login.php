<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Gateway - The Noble Groom</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --color-bg-dark: #121620;
            --color-primary: #D4AF37;
        }
        body {
            background-color: var(--color-bg-dark);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .form-control:focus {
            border-color: var(--color-primary) !important;
            box-shadow: 0 0 0 4px rgba(214, 175, 55, 0.15) !important;
        }
        .btn-gold {
            background-color: var(--color-bg-dark);
            color: #ffffff;
            border: 1px solid var(--color-primary);
            transition: all 0.25s ease;
        }
        .btn-gold:hover {
            background-color: var(--color-primary);
            color: var(--color-bg-dark);
            font-weight: bold;
        }
    </style>
</head>
<body class="p-3">

<div class="login-card p-4 p-sm-5">
    <div class="text-center mb-4">
        <span class="fs-1 d-block mb-2">💈</span>
        <h3 class="fw-bold text-dark mb-1">Noble Console</h3>
        <p class="text-muted small">Enter administrative credentials to gain entry</p>
    </div>

    <?php if (isset($_SESSION['login_error'])): ?>
        <div class="alert alert-danger border-0 rounded-3 small py-2 mb-3" role="alert">
            <?= htmlspecialchars($_SESSION['login_error']) ?>
        </div>
        <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <form action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/admin/login" method="POST">
        <!-- Secure CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="form-floating mb-3">
            <input type="text" class="form-control rounded-3" id="username" name="username" placeholder="Username" required autocomplete="username">
            <label for="username">Username</label>
        </div>

        <div class="form-floating mb-4">
            <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Password" required autocomplete="current-password">
            <label for="password">Password</label>
        </div>

        <button type="submit" class="btn btn-gold w-100 py-3 rounded-pill fw-bold mb-3">
            Authorize Entry
        </button>
        
        <div class="text-center">
            <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/" class="text-decoration-none small text-muted">← Back to booking page</a>
        </div>
    </form>
</div>

</body>
</html>