<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - The Noble Groom</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --color-primary: #D4AF37;
            --color-bg-dark: #121620;
            --color-bg-gray: #F8F9FB;
        }
        body {
            background-color: var(--color-bg-gray);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .admin-nav {
            background-color: var(--color-bg-dark);
            border-bottom: 2px solid var(--color-primary);
        }
        .metric-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }
        /* Mobile List-Card Style */
        .appointment-item-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #E5E7EB;
            transition: transform 0.2s ease;
        }
        .appointment-item-card:active {
            transform: scale(0.98);
        }
        .status-badge-Confirmed { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .status-badge-Completed { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
        .status-badge-Cancelled { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
        
        /* Modern Mobile View adjustments */
        @media (max-width: 767.98px) {
            .metric-title { font-size: 0.75rem; }
            .metric-value { font-size: 1.25rem !important; }
        }
    </style>
</head>
<body class="pb-5">

<!-- Secure Navigation Header -->
<nav class="navbar admin-nav navbar-dark px-3 py-3 mb-4">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <span class="navbar-brand fw-bold fs-5 mb-0">Noble Console</span>
        <form action="<?= \BASE_PATH ?>/admin/logout" method="POST" class="m-0">
            <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3">Logout</button>
        </form>
    </div>
</nav>

<div class="container px-3">
    
    <!-- Top Selector and Control -->
    <div class="row align-items-center mb-4 g-3">
        <div class="col-6">
            <h5 class="fw-bold text-dark mb-0">Daily Schedule</h5>
            <small class="text-muted"><?= date('l, M d, Y', strtotime($selected_date)) ?></small>
        </div>
        <div class="col-6 text-end">
            <form method="GET" action="<?= \BASE_PATH ?>/admin">
                <input type="date" 
                       class="form-control form-control-sm text-center fw-bold shadow-sm d-inline-block w-auto rounded-pill border-secondary" 
                       name="date" 
                       value="<?= $selected_date ?>" 
                       onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <!-- Quick Stats Metric Rows -->
    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
        <div class="col">
            <div class="metric-card p-3 text-center">
                <span class="text-muted d-block small mb-1 metric-title">TOTAL BOOKED</span>
                <strong class="fs-4 text-dark metric-value"><?= intval($metrics['total'] ?? 0) ?></strong>
            </div>
        </div>
        <div class="col">
            <div class="metric-card p-3 text-center">
                <span class="text-muted d-block small mb-1 metric-title text-primary">CONFIRMED</span>
                <strong class="fs-4 text-primary metric-value"><?= intval($metrics['confirmed'] ?? 0) ?></strong>
            </div>
        </div>
        <div class="col">
            <div class="metric-card p-3 text-center">
                <span class="text-muted d-block small mb-1 metric-title text-success">COMPLETED</span>
                <strong class="fs-4 text-success metric-value"><?= intval($metrics['completed'] ?? 0) ?></strong>
            </div>
        </div>
        <div class="col">
            <div class="metric-card p-3 text-center">
                <span class="text-muted d-block small mb-1 metric-title text-warning">REVENUE</span>
                <strong class="fs-4 text-dark metric-value">$<?= number_format($revenue, 2) ?></strong>
            </div>
        </div>
    </div>

    <!-- Live Appointment list -->
    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px; font-size: 0.75rem;">Appointments Queue</h6>

    <?php if (!empty($appointments)): ?>
        <div class="d-flex flex-column gap-3">
            <?php foreach ($appointments as $apt): ?>
                <div class="appointment-item-card p-3 shadow-sm d-flex justify-content-between align-items-center">
                    <div>
                        <!-- Time Segment -->
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-pill py-1 px-3 status-badge-<?= $apt['status'] ?> fw-bold small">
                                <?= $apt['status'] ?>
                            </span>
                            <span class="font-monospace fw-bold text-dark">
                                <?= date('h:i A', strtotime($apt['appointment_time'])) ?>
                            </span>
                        </div>
                        <!-- Customer Credentials -->
                        <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($apt['customer_name']) ?></h6>
                        <span class="text-muted small d-block mb-1"><?= htmlspecialchars($apt['phone']) ?></span>
                        <span class="badge bg-light text-dark border py-1.5 px-2 rounded-2 small" style="font-size: 0.7rem;">
                            <?= html_entity_decode(htmlspecialchars($apt['service']), ENT_QUOTES) ?>
                        </span>
                    </div>

                    <!-- Manage Actions Segment -->
                    <div class="text-end">
                        <!-- Modern dataset binding prevents JS syntax failures -->
                        <button class="btn btn-outline-dark btn-sm rounded-pill fw-semibold px-3 update-action-btn" 
                                data-id="<?= $apt['id'] ?>"
                                data-name="<?= htmlspecialchars($apt['customer_name'], ENT_QUOTES, 'UTF-8') ?>"
                                data-status="<?= htmlspecialchars($apt['status'], ENT_QUOTES, 'UTF-8') ?>">
                            Update
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white border rounded-4">
            <span class="fs-2">💤</span>
            <h6 class="fw-bold text-dark mt-2 mb-1">No bookings today</h6>
            <p class="text-muted small mb-0">Use the calendar to navigate different dates.</p>
        </div>
    <?php endif; ?>

</div>

<!-- ================= MOBILE-FIRST QUICK UPDATE SHEET ================= -->
<div class="modal fade" id="manageSheet" tabindex="-1" aria-labelledby="manageSheetLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered px-3">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 px-4 pt-3 pb-0">
                <div>
                    <h5 class="fw-bold text-dark mb-0" id="manageSheetLabel">Update Appointment</h5>
                    <p class="text-muted small mb-0" id="modal_client_name"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="<?= \BASE_PATH ?>/admin/update-status" method="POST">
                <!-- CSRF and Redirect data fields -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="appointment_id" id="modal_apt_id">
                <input type="hidden" name="redirect_date" value="<?= $selected_date ?>">

                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Adjust the status of the client appointment based on their attendance.</p>
                    
                    <div class="d-flex flex-column gap-2">
                        <!-- Confirmed Radio Button -->
                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer" style="cursor:pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge rounded-pill px-3 py-1.5 status-badge-Confirmed">Confirmed</span>
                                <span class="small text-muted">Awaiting customer arrival</span>
                            </div>
                            <input type="radio" name="status" value="Confirmed" id="status_confirmed">
                        </label>

                        <!-- Completed Radio Button -->
                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer" style="cursor:pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge rounded-pill px-3 py-1.5 status-badge-Completed">Completed</span>
                                <span class="small text-muted">Service done and paid</span>
                            </div>
                            <input type="radio" name="status" value="Completed" id="status_completed">
                        </label>

                        <!-- Cancelled Radio Button -->
                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer" style="cursor:pointer;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge rounded-pill px-3 py-1.5 status-badge-Cancelled">Cancelled</span>
                                <span class="small text-muted">No show or early cancellation</span>
                            </div>
                            <input type="radio" name="status" value="Cancelled" id="status_cancelled">
                        </label>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3 bg-light d-flex gap-2">
                    <button type="button" class="btn btn-light border px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-dark px-4 rounded-pill fw-bold" style="background: var(--color-bg-dark);">Save Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle (CRITICAL FIX FOR THE MODAL SHEETS FLOW) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Find all update buttons and attach click listeners dynamically
        const updateButtons = document.querySelectorAll('.update-action-btn');
        const manageSheetModal = new bootstrap.Modal(document.getElementById('manageSheet'));

        updateButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Read clean dataset attributes
                const aptId = this.getAttribute('data-id');
                const clientName = this.getAttribute('data-name');
                const currentStatus = this.getAttribute('data-status');

                // Bind elements in modal sheet
                document.getElementById('modal_apt_id').value = aptId;
                document.getElementById('modal_client_name').innerText = "Client: " + clientName;

                // Reset all radio inputs
                document.getElementById('status_confirmed').checked = false;
                document.getElementById('status_completed').checked = false;
                document.getElementById('status_cancelled').checked = false;

                // Auto-select corresponding radio matching current state
                if (currentStatus === 'Confirmed') {
                    document.getElementById('status_confirmed').checked = true;
                } else if (currentStatus === 'Completed') {
                    document.getElementById('status_completed').checked = true;
                } else if (currentStatus === 'Cancelled') {
                    document.getElementById('status_cancelled').checked = true;
                }

                // Smoothly trigger modal sheet presentation
                manageSheetModal.show();
            });
        });
    });
</script>

</body>
</html>