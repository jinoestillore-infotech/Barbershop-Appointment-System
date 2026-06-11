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
            --color-primary: #D4AF37;       /* Antique Brass Gold */
            --color-primary-dark: #B3922E;  /* Deep Gold */
            --color-bg-dark: #121620;       /* Obsidian Black */
            --color-bg-gray: #F8F9FB;       /* Platinum White */
            --font-serif: 'Playfair Display', Georgia, serif;
        }
        
        body {
            background-color: var(--color-bg-gray);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1F2937;
        }

        .font-luxury {
            font-family: var(--font-serif);
        }

        /* Compact Luxury Navigation Header */
        .admin-nav {
            background-color: var(--color-bg-dark);
            border-bottom: 2px solid var(--color-primary);
            padding: 16px;
        }

        /* 2x2 Metric Grid Cards */
        .metric-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
            padding: 16px 12px;
            transition: all 0.2s ease;
        }
        
        /* Interactive Date Scroller for Easy Thumb-Tapping on Mobile */
        .date-scroller {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            gap: 10px;
            padding: 4px 4px 16px 4px;
            -webkit-overflow-scrolling: touch;
        }
        .date-scroller::-webkit-scrollbar {
            height: 4px;
        }
        .date-scroller::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.08);
            border-radius: 10px;
        }

        .date-pill {
            flex: 0 0 auto;
            width: 60px;
            height: 76px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #E5E7EB;
            text-decoration: none;
            transition: all 0.25s ease;
        }
        .date-pill.active {
            background: var(--color-bg-dark);
            border-color: var(--color-bg-dark);
            box-shadow: 0 8px 16px rgba(18, 22, 32, 0.15);
        }
        .date-pill.active .dp-day,
        .date-pill.active .dp-num {
            color: #ffffff !important;
        }

        /* Mobile queue cards styling */
        .appointment-item-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            padding: 18px;
            margin-bottom: 12px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            -webkit-tap-highlight-color: transparent;
        }
        .appointment-item-card:active {
            transform: scale(0.98);
            background-color: #FAFAFA;
        }

        /* Status Badge Variants */
        .status-badge-Confirmed { background-color: rgba(13, 110, 253, 0.08); color: #0d6efd; }
        .status-badge-Completed { background-color: rgba(25, 135, 84, 0.08); color: #198754; }
        .status-badge-Cancelled { background-color: rgba(220, 53, 69, 0.08); color: #dc3545; }
        
        /* Floating labels inputs */
        .form-control, .form-select {
            font-size: 16px !important; /* Prevents iOS auto-zoom on click */
            border-radius: 12px !important;
        }

        /* ================= MOBILE-FIRST LAYOUT ENGINE ================= */
        @media (max-width: 767.98px) {
            .metric-title {
                font-size: 0.65rem;
                letter-spacing: 0.5px;
            }
            .metric-value {
                font-size: 1.15rem !important;
            }

            /* Vertically center the update sheet on small mobile displays */
            .modal-centered-mobile {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                margin: 16px !important;
                min-height: calc(100% - 32px) !important;
                max-width: 480px !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            .modal-centered-mobile .modal-content {
                border-radius: 24px !important;
                border: none !important;
                width: 100% !important;
                max-height: 85vh !important; /* Forces bounding layout when soft-keyboard opens */
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }

            .modal-centered-mobile .modal-body {
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
                max-height: 55vh !important; /* Keeps custom numeric keyboard inputs safe from layout overflow */
                padding: 24px !important;
            }
        }
    </style>
</head>
<body class="pb-5">

<!-- Secure Compact Admin Navigation Header -->
<nav class="navbar admin-nav navbar-dark mb-4">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <div>
                <span class="navbar-brand fw-bold fs-6 mb-0 d-block leading-none">NOBLE CONSOLE</span>
                <span class="text-uppercase tracking-widest text-white-50" style="font-size: 0.55rem; letter-spacing: 0.15em;">Barber Dashboard</span>
            </div>
        </div>
        <form action="<?= \BASE_PATH ?>/admin/logout" method="POST" class="m-0">
            <button type="submit" class="btn btn-outline-light btn-xs rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">Logout</button>
        </form>
    </div>
</nav>

<div class="container px-3">
    
    <!-- Title & Date Selector Fallback -->
    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
        <div>
            <h5 class="fw-bold text-dark mb-0">Daily Schedule</h5>
            <small class="text-muted"><?= date('l, F d', strtotime($selected_date)) ?></small>
        </div>
        <form method="GET" action="<?= \BASE_PATH ?>/admin" class="d-flex align-items-center">
            <label for="date-manual-picker" class="text-primary fw-semibold small d-flex align-items-center gap-1 cursor-pointer" style="cursor: pointer; color: var(--color-primary-dark) !important;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-event" viewBox="0 0 16 16">
                    <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                </svg>
                <span>Calendar</span>
            </label>
            <input type="date" 
                   id="date-manual-picker" 
                   name="date" 
                   value="<?= $selected_date ?>" 
                   onchange="this.form.submit()" 
                   style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;">
        </form>
    </div>

    <!-- Dynamic Quick Date Horizontal Scroller (Next 7 Business Days) -->
    <div class="date-scroller mb-4">
        <?php 
        // Anchoring calculation to today midnight to avoid hour alignment/timezone duplication errors
        $today_midnight = strtotime('today midnight');
        for ($i = -1; $i < 6; $i++): 
            // FIXED: Using "$i days" instead of "+$i days" to prevent double sign conflicts like "+-1 days" which parsed as "+1 days" in strtotime
            $day_timestamp = strtotime("$i days", $today_midnight);
            $day_code = date('Y-m-d', $day_timestamp);
            $day_name = date('D', $day_timestamp);
            $day_num = date('d', $day_timestamp);
            $is_selected = ($day_code === $selected_date);
        ?>
            <a href="<?= \BASE_PATH ?>/admin?date=<?= $day_code ?>" 
               class="date-pill <?= $is_selected ? 'active' : '' ?>">
                <span class="dp-day text-uppercase fw-semibold text-muted mb-1" style="font-size: 0.65rem;"><?= $day_name ?></span>
                <span class="dp-num fw-bold fs-6 text-dark"><?= $day_num ?></span>
            </a>
        <?php endfor; ?>
    </div>

    <!-- Quick Stats Metric 2x2 Grid Layout for Mobile Screens -->
    <div class="row row-cols-2 g-3 mb-4">
        <div class="col">
            <div class="metric-card text-center">
                <span class="text-muted d-block small mb-1 metric-title fw-bold text-uppercase">Total Queue</span>
                <strong class="fs-4 text-dark metric-value"><?= intval($metrics['total'] ?? 0) ?></strong>
            </div>
        </div>
        <div class="col">
            <div class="metric-card text-center">
                <span class="text-muted d-block small mb-1 metric-title fw-bold text-uppercase text-primary">Confirmed</span>
                <strong class="fs-4 text-primary metric-value"><?= intval($metrics['confirmed'] ?? 0) ?></strong>
            </div>
        </div>
        <div class="col">
            <div class="metric-card text-center">
                <span class="text-muted d-block small mb-1 metric-title fw-bold text-uppercase text-success">Completed</span>
                <strong class="fs-4 text-success metric-value"><?= intval($metrics['completed'] ?? 0) ?></strong>
            </div>
        </div>
        <div class="col">
            <div class="metric-card text-center" style="border-color: rgba(212, 175, 55, 0.3);">
                <span class="text-muted d-block small mb-1 metric-title fw-bold text-uppercase" style="color: var(--color-primary-dark) !important;">Revenue</span>
                <strong class="fs-4 text-dark metric-value font-monospace fw-bold">$<?= number_format($revenue, 2) ?></strong>
            </div>
        </div>
    </div>

    <!-- Live Appointment list -->
    <h6 class="fw-bold text-muted text-uppercase mb-3 px-1" style="letter-spacing: 0.8px; font-size: 0.7rem;">Timeline Schedule</h6>

    <?php if (!empty($appointments)): ?>
        <div class="d-flex flex-column">
            <?php foreach ($appointments as $apt): ?>
                <div class="appointment-item-card d-flex justify-content-between align-items-center">
                    <div>
                        <!-- Time Segment -->
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-pill py-1 px-2.5 status-badge-<?= $apt['status'] ?> fw-bold" style="font-size: 0.65rem;">
                                <?= $apt['status'] ?>
                            </span>
                            <span class="font-monospace fw-bold text-dark" style="font-size: 0.9rem;">
                                <?= date('h:i A', strtotime($apt['appointment_time'])) ?>
                            </span>
                            <?php if ($apt['status'] === 'Completed' && floatval($apt['price_paid']) > 0): ?>
                                <span class="badge bg-success text-white fw-bold font-monospace" style="font-size: 0.65rem;">
                                    +$<?= number_format($apt['price_paid'], 2) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <!-- Customer Credentials -->
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;"><?= htmlspecialchars($apt['customer_name']) ?></h6>
                        <span class="text-muted small d-block mb-2 font-monospace" style="font-size: 0.8rem;"><?= htmlspecialchars($apt['phone']) ?></span>
                        <span class="badge bg-light text-dark border py-1.5 px-2.5 rounded-2" style="font-size: 0.7rem;">
                            <?= html_entity_decode(htmlspecialchars($apt['service']), ENT_QUOTES) ?>
                        </span>
                    </div>

                    <!-- Manage Actions Segment (Target Thumb Sized for Mobile) -->
                    <div class="text-end ps-2">
                        <button class="btn btn-dark btn-sm rounded-pill fw-bold px-3 py-2 update-action-btn shadow-sm" 
                                style="background-color: var(--color-bg-dark); border-color: var(--color-primary); font-size: 0.8rem;"
                                data-id="<?= $apt['id'] ?>"
                                data-name="<?= htmlspecialchars($apt['customer_name'], ENT_QUOTES, 'UTF-8') ?>"
                                data-status="<?= htmlspecialchars($apt['status'], ENT_QUOTES, 'UTF-8') ?>"
                                data-price="<?= htmlspecialchars($apt['price_paid'], ENT_QUOTES, 'UTF-8') ?>">
                            Update
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white border rounded-4 shadow-sm mx-1">
            <span class="fs-1 d-block mb-2">💤</span>
            <h6 class="fw-bold text-dark mb-1">No Bookings Today</h6>
            <p class="text-muted small mb-0 px-4">Use the calendar or scroll the date strip to navigate different dates.</p>
        </div>
    <?php endif; ?>

</div>

<!-- ================= MOBILE-FIRST QUICK UPDATE SHEET (KEYBOARD SECURED) ================= -->
<div class="modal fade" id="manageSheet" tabindex="-1" aria-labelledby="manageSheetLabel" aria-hidden="true">
    <div class="modal-dialog modal-centered-mobile modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            
            <div class="modal-header border-0 px-4 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0" id="manageSheetLabel" style="font-size: 1.15rem;">Update Appointment</h5>
                    <p class="text-muted small mb-0" id="modal_client_name" style="font-size: 0.85rem; font-weight: 600; color: var(--color-primary-dark) !important;"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="<?= \BASE_PATH ?>/admin/update-status" method="POST">
                <!-- CSRF and Redirect data fields -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="appointment_id" id="modal_apt_id">
                <input type="hidden" name="redirect_date" value="<?= $selected_date ?>">

                <div class="modal-body">
                    <p class="text-muted small mb-3" style="font-size: 0.8rem;">Adjust the status of the client appointment based on their attendance.</p>
                    
                    <div class="d-flex flex-column gap-2 mb-4">
                        <!-- Confirmed Radio Button -->
                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer" style="cursor:pointer; background-color: #FAFAFA;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge rounded-pill px-3 py-1.5 status-badge-Confirmed" style="font-size: 0.7rem;">Confirmed</span>
                                <span class="small text-muted" style="font-size: 0.75rem;">Awaiting arrival</span>
                            </div>
                            <input type="radio" name="status" value="Confirmed" id="status_confirmed" class="form-check-input">
                        </label>

                        <!-- Completed Radio Button -->
                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer" style="cursor:pointer; background-color: #FAFAFA;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge rounded-pill px-3 py-1.5 status-badge-Completed" style="font-size: 0.7rem;">Completed</span>
                                <span class="small text-muted" style="font-size: 0.75rem;">Done and paid</span>
                            </div>
                            <input type="radio" name="status" value="Completed" id="status_completed" class="form-check-input">
                        </label>

                        <!-- Cancelled Radio Button -->
                        <label class="border rounded-3 p-3 d-flex align-items-center justify-content-between cursor-pointer" style="cursor:pointer; background-color: #FAFAFA;">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge rounded-pill px-3 py-1.5 status-badge-Cancelled" style="font-size: 0.7rem;">Cancelled</span>
                                <span class="small text-muted" style="font-size: 0.75rem;">No show</span>
                            </div>
                            <input type="radio" name="status" value="Cancelled" id="status_cancelled" class="form-check-input">
                        </label>
                    </div>

                    <!-- Custom Price Input Field -->
                    <div class="mb-2">
                        <label for="modal_price_paid" class="form-label fw-bold text-muted small" style="font-size: 0.75rem;">AMOUNT PAID ($)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-bold">$</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-lg fw-bold" name="price_paid" id="modal_price_paid" placeholder="0.00" style="font-size: 1.1rem; height: auto !important;">
                        </div>
                        <small class="text-muted mt-2 d-block" style="font-size: 0.75rem; line-height: 1.3;">This amount will sum into your daily system revenue metrics when the status is set to Completed.</small>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3 bg-light d-flex gap-2">
                    <button type="button" class="btn btn-light border px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-dark px-4 rounded-pill fw-bold" style="background: var(--color-bg-dark); border-color: var(--color-primary);">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateButtons = document.querySelectorAll('.update-action-btn');
        const manageSheetModal = new bootstrap.Modal(document.getElementById('manageSheet'));

        // Attach listeners to update triggers
        updateButtons.forEach(button => {
            button.addEventListener('click', function() {
                const aptId = this.getAttribute('data-id');
                const clientName = this.getAttribute('data-name');
                const currentStatus = this.getAttribute('data-status');
                const currentPrice = this.getAttribute('data-price') || "0.00";

                document.getElementById('modal_apt_id').value = aptId;
                document.getElementById('modal_client_name').innerText = "Client: " + clientName;
                document.getElementById('modal_price_paid').value = parseFloat(currentPrice).toFixed(2);

                document.getElementById('status_confirmed').checked = false;
                document.getElementById('status_completed').checked = false;
                document.getElementById('status_cancelled').checked = false;

                if (currentStatus === 'Confirmed') {
                    document.getElementById('status_confirmed').checked = true;
                } else if (currentStatus === 'Completed') {
                    document.getElementById('status_completed').checked = true;
                } else if (currentStatus === 'Cancelled') {
                    document.getElementById('status_cancelled').checked = true;
                }

                manageSheetModal.show();
            });
        });

        // Center scroll active date pill on page load
        var activePill = document.querySelector('.date-pill.active');
        if (activePill) {
            var scroller = document.querySelector('.date-scroller');
            scroller.scrollLeft = activePill.offsetLeft - (scroller.clientWidth / 2) + (activePill.clientWidth / 2);
        }
    });
</script>

</body>
</html>