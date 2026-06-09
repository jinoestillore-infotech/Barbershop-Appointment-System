<?php require_once __DIR__ . '/../includes/header.php'; ?>

<!-- High-Fidelity Custom Mobile-First System Styles -->
<style>
    :root {
        --color-primary: #D4AF37;       /* Antique Brass Gold */
        --color-primary-dark: #B3922E;  /* Luxury Gold Accent */
        --color-bg-dark: #121620;       /* Deep Obsidian Black */
        --color-bg-card: #ffffff;
        --color-text-muted: #6B7280;
        --font-serif: 'Playfair Display', Georgia, serif;
    }

    body {
        background-color: #F8F9FB !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    /* Elegant Serif Branding Font Fallback */
    .font-luxury {
        font-family: var(--font-serif);
    }

    /* Date Scroller - Premium Horizontal Swipe Control */
    .date-scroller {
        display: flex;
        overflow-x: auto;
        scroll-behavior: smooth;
        gap: 12px;
        padding: 8px 4px 16px 4px;
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
        width: 68px;
        height: 84px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid #E5E7EB;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .date-pill:hover {
        border-color: var(--color-primary);
        color: inherit;
    }
    .date-pill.active {
        background: var(--color-bg-dark);
        border-color: var(--color-bg-dark);
        box-shadow: 0 10px 20px rgba(18, 22, 32, 0.15);
    }
    .date-pill.active .dp-day,
    .date-pill.active .dp-num {
        color: #ffffff !important;
    }

    /* Premium Time Slot Card layouts */
    .time-slot-card {
        background: var(--color-bg-card);
        border: 1px solid #E5E7EB;
        border-radius: 20px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }
    .time-slot-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: transparent;
        transition: background 0.25s ease;
    }
    
    /* Interactive States for Available Cards */
    .time-slot-card:not(.slot-booked):hover {
        transform: translateY(-4px);
        border-color: var(--color-primary);
        box-shadow: 0 12px 24px rgba(212, 175, 55, 0.12);
    }
    .time-slot-card:not(.slot-booked):hover::before {
        background: var(--color-primary);
    }
    .time-slot-card:not(.slot-booked):active {
        transform: scale(0.97);
    }

    /* Booked Status Style Overrides */
    .slot-booked {
        background: #F3F4F6;
        border-color: #E5E7EB;
        cursor: not-allowed;
    }
    .slot-booked .time-text {
        color: #9CA3AF !important;
    }

    /* Mobile Floating Labels Optimization (Prevents browser system zoom penalty) */
    .form-control, .form-select {
        font-size: 16px !important; /* Forces mobile browsers to not zoom on tap */
        height: 54px !important;
        border-radius: 12px !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--color-primary) !important;
        box-shadow: 0 0 0 4px rgba(214, 175, 55, 0.15) !important;
    }

    /* Premium Boarding Ticket Receipt Layout */
    .ticket {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 420px;
        margin: 0 auto;
    }
    .ticket-header-strip {
        background: var(--color-bg-dark);
        color: #ffffff;
        padding: 24px 20px;
        position: relative;
    }
    .ticket-body-strip {
        padding: 28px 24px;
        background: #ffffff;
    }
    /* Ticket Tear Perforations styling */
    .ticket-tear-line {
        position: relative;
        height: 2px;
        border-top: 2px dashed #E5E7EB;
        margin: 24px 0;
    }
    .ticket-tear-line::before,
    .ticket-tear-line::after {
        content: '';
        position: absolute;
        top: -10px;
        width: 20px;
        height: 20px;
        background: rgba(0, 0, 0, 0.5); /* Seamless blend with high contrast background overlays */
        border-radius: 50%;
    }
    .ticket-tear-line::before { left: -35px; }
    .ticket-tear-line::after { right: -35px; }

    /* Custom Simulated Barcode element */
    .ticket-barcode {
        height: 52px;
        background: repeating-linear-gradient(
            90deg,
            #111827 0px, #111827 2px,
            transparent 2px, transparent 5px,
            #111827 5px, #111827 9px,
            transparent 9px, transparent 11px,
            #111827 11px, #111827 13px,
            transparent 13px, transparent 16px
        );
        opacity: 0.85;
        width: 80%;
        margin: 0 auto;
    }

    /* ================= FULLY MOBILE RESPONSIVE LAYOUT ENGINE ================= */
    @media (max-width: 767.98px) {
        /* Center modals elegantly on mobile screens to protect against keyboard cutoffs */
        .modal-centered-mobile {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 12px !important;
            min-height: calc(100% - 25px) !important;
            max-width: 480px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Keyboard responsive flex card structure */
        .modal-centered-mobile .modal-content {
            border-radius: 24px !important;
            border: none !important;
            width: 100% !important;
            max-height: 85vh !important; /* Forces layout scaling when virtual keyboard emerges */
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3) !important;
        }

        /* Internal Elastic Touch Safe Scrolling */
        .modal-centered-mobile .modal-body {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
            max-height: 60vh !important; /* Prevents inputs and confirm buttons from clashing */
            padding: 20px 24px 24px 24px !important;
        }

        /* Success ticket adjustments for mobile screenshooting */
        .ticket {
            width: calc(100% - 16px) !important;
            border-radius: 20px !important;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }
        .ticket-tear-line::before { left: -34px !important; }
        .ticket-tear-line::after { right: -34px !important; }
    }
</style>

<!-- Top Premium Brand / Header Block -->
<div class="row justify-content-center text-center px-3 mt-4 mb-4">
    <div class="col-12 col-md-8">
        <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
            <span style="width: 24px; height: 1px; background: var(--color-primary);"></span>
            <span class="text-uppercase tracking-widest fw-semibold text-muted" style="font-size: 0.7rem; letter-spacing: 0.25em;">Est. 2026</span>
            <span style="width: 24px; height: 1px; background: var(--color-primary);"></span>
        </div>
        <h1 class="font-luxury fw-bold display-5 text-dark mb-2">THE NOBLE BARBER</h1>
        <p class="text-muted small mx-auto mb-4" style="max-width: 480px; font-size: 0.9rem;">
            Experience unparalleled precision barbering. Secure your elite personal grooming session below.
        </p>
    </div>
</div>

<!-- Dynamic Horizontal Calendar Scroller (Displays next 7 booking days) -->
<div class="container px-2 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
        <h6 class="fw-bold text-dark uppercase mb-0" style="letter-spacing: 0.5px; font-size: 0.85rem;">Select Date</h6>
    </div>

    <div class="date-scroller">
        <?php 
        for ($i = 0; $i < 7; $i++): 
            $day_timestamp = strtotime("+$i days");
            $day_code = date('Y-m-d', $day_timestamp);
            $day_name = date('D', $day_timestamp);
            $day_num = date('d', $day_timestamp);
            $is_selected = ($day_code === $selected_date);
        ?>
            <a href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/?date=<?= $day_code ?>" 
               class="date-pill <?= $is_selected ? 'active' : '' ?>">
                <span class="dp-day text-uppercase fw-semibold text-muted mb-1" style="font-size: 0.7rem;"><?= $day_name ?></span>
                <span class="dp-num fw-bold fs-5 text-dark"><?= $day_num ?></span>
            </a>
        <?php endfor; ?>
    </div>
</div>

<!-- Sessions Status Header -->
<div class="container px-2 mb-3">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">Available Sessions</h6>
        <span class="small text-muted fw-semibold">
            <?= date('l, M d', strtotime($selected_date)) ?>
        </span>
    </div>
</div>

<!-- Modern Compact Grid (Optimized for double columns on touch interfaces) -->
<div class="container px-2 mb-5">
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
        <?php foreach ($time_slots as $slot): ?>
            <?php 
                $slot_hour_min = substr($slot['start'], 0, 5);
                $is_booked = in_array($slot_hour_min, $booked_times);
            ?>
            <div class="col">
                <div onclick="<?= $is_booked ? '' : "openBookingModal('{$slot['start']}', '{$slot['label']}')" ?>" 
                     class="time-slot-card h-100 <?= $is_booked ? 'slot-booked' : '' ?>">
                    <div class="card-body p-3 text-center d-flex flex-column justify-content-between">
                        
                        <!-- Beautiful Clean SVGs for Status Verification -->
                        <div class="d-flex justify-content-center mb-3">
                            <?php if ($is_booked): ?>
                                <span class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                    </svg>
                                </span>
                            <?php else: ?>
                                <span class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                    </svg>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Slot Time Label -->
                        <span class="time-text font-monospace fw-bold text-dark d-block mb-1" style="font-size: 0.95rem; letter-spacing: -0.5px;">
                            <?= htmlspecialchars($slot['label']) ?>
                        </span>

                        <span class="small fw-semibold text-uppercase d-block" style="font-size: 0.65rem; letter-spacing: 0.5px; color: <?= $is_booked ? '#9CA3AF' : 'var(--color-primary-dark)' ?>">
                            <?= $is_booked ? 'Reserved' : 'Book Session' ?>
                        </span>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ================= PREMIUM NATIVE-STYLE MODALS ================= -->

<!-- 1. Booking Sheet (Slides Up as a Drawer on Mobile Devices) -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-centered-mobile modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            
            <!-- Handle bar touch indicator for mobile drawers -->
            <div class="d-block d-sm-none text-center pt-3 pb-1">
                <span class="bg-secondary opacity-25 rounded-pill" style="display: inline-block; width: 44px; height: 5px;"></span>
            </div>

            <div class="modal-header border-0 px-4 pt-2 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0" id="bookingModalLabel" style="font-size: 1.15rem;">Secure Your Appointment</h5>
                    <p class="text-muted small mb-0" style="font-size: 0.8rem;">The Noble Groom Private Reservation</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/book" method="POST">
                <!-- Validation Metadata Payload -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="appointment_date" value="<?= $selected_date ?>">
                <input type="hidden" name="appointment_time" id="modal_slot_time">
                <input type="hidden" name="appointment_time_label" id="modal_slot_label">

                <div class="modal-body">
                    
                    <!-- Beautiful Overview Slot Card -->
                    <div class="bg-light border rounded-3 p-3 mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted d-block uppercase fw-semibold" style="font-size: 0.6rem; letter-spacing: 0.5px;">APPOINTMENT TIME</span>
                            <span class="fw-bold font-monospace text-dark fs-5" id="display_booking_time"></span>
                        </div>
                        <div class="text-end">
                            <span class="text-muted d-block uppercase fw-semibold" style="font-size: 0.6rem; letter-spacing: 0.5px;">DATE</span>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;"><?= date('M d, Y', strtotime($selected_date)) ?></span>
                        </div>
                    </div>

                    <!-- Floating Form Input Fields (Touch Target Optimized) -->
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="John Doe" required autocomplete="name">
                        <label for="customer_name" class="fw-medium">Full Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="09171234567" required autocomplete="tel">
                        <label for="phone" class="fw-medium">Mobile Number</label>
                    </div>

                    <div class="form-floating mb-4">
                        <select class="form-select" id="service" name="service" required>
                            <option value="Barber's Cut">Barber's Cut</option>
                            <option value="Fade Cut">Fade Cut</option>
                            <option value="Skin Fade">Skin Fade</option>
                            <option value="Taper Fade">Taper Fade</option>
                            <option value="Crew Cut">Crew Cut</option>
                            <option value="Low Fade">Low Fade</option>
                            <option value="Mid Fade">Mid Fade</option>
                            <option value="High Fade">High Fade</option>
                            <option value="Two Block Cut">Two Block Cut</option>
                            <option value="Korean Comma Hair">Korean Comma Hair</option>
                            <option value="French Crop">French Crop</option>
                            <option value="Textured Crop">Textured Crop</option>
                            <option value="Undercut">Undercut</option>
                            <option value="Pompadour">Pompadour</option>
                            <option value="Side Part">Side Part</option>
                            <option value="Buzz Cut">Buzz Cut</option>
                            <option value="Slick Back">Slick Back</option>
                            <option value="Wolf Cut">Wolf Cut</option>
                            <option value="Mullet Fade">Mullet Fade</option>
                        </select>
                        <label for="service" class="fw-medium">Select Hairstyle</label>
                    </div>

                    <!-- Lateness Rules Warning Frame -->
                    <div class="bg-danger-subtle text-danger border border-danger-subtle rounded-3 p-3 mb-1 d-flex gap-2">
                        <span style="font-size: 1.1rem; line-height: 1;">⚠️</span>
                        <div class="small" style="font-size: 0.8rem;">
                            <strong class="d-block mb-1">Strict Attendance Rule</strong>
                            We execute highly detailed services back-to-back. Lateness of more than <strong>2-3 minutes</strong> will result in immediate slot cancellation for waiting walk-ins.
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 p-3 bg-light d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-light px-4 py-2 border rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark px-4 py-2 rounded-pill fw-bold" style="background: var(--color-bg-dark);">Confirm & Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Interactive VIP Perforated Ticket Modal -->
<?php if (isset($_SESSION['booking_success'])): ?>
    <?php $success_data = $_SESSION['booking_success']; ?>
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered px-2">
            <div class="modal-content bg-transparent border-0">
                
                <div class="ticket">
                    <!-- Ticket Header Base -->
                    <div class="ticket-header-strip text-center">
                        <div class="bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center rounded-circle p-2 mb-3" style="width: 48px; height: 48px; background: rgba(212, 175, 55, 0.15) !important;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--color-primary)" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                        </div>
                        <h4 class="font-luxury fw-bold mb-1" style="letter-spacing: 0.5px; font-size: 1.25rem;">SESSION SECURED</h4>
                        <span class="text-white-50 uppercase tracking-widest fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.2em;">THE NOBLE GROOM CLUB</span>
                    </div>

                    <!-- Ticket Core Metadata Details -->
                    <div class="ticket-body-strip">
                        
                        <!-- Client Owner & Chosen service line -->
                        <div class="row mb-3 g-2">
                            <div class="col-6">
                                <span class="text-muted d-block uppercase fw-semibold" style="font-size: 0.6rem; letter-spacing: 0.5px;">CLIENT OWNER</span>
                                <strong class="text-dark d-block text-truncate" style="font-size: 0.95rem;"><?= htmlspecialchars($success_data['name']) ?></strong>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-muted d-block uppercase fw-semibold" style="font-size: 0.6rem; letter-spacing: 0.5px;">GROOMING STYLE</span>
                                <strong class="text-dark d-block text-truncate" style="font-size: 0.95rem;"><?= htmlspecialchars($success_data['service']) ?></strong>
                            </div>
                        </div>

                        <!-- Date & Selected Time slot line -->
                        <div class="row g-2">
                            <div class="col-6">
                                <span class="text-muted d-block uppercase fw-semibold" style="font-size: 0.6rem; letter-spacing: 0.5px;">DATE RESERVED</span>
                                <strong class="text-dark d-block" style="font-size: 0.95rem;"><?= htmlspecialchars($success_data['date']) ?></strong>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-muted d-block uppercase fw-semibold" style="font-size: 0.6rem; letter-spacing: 0.5px;">ASSIGNED TIMELINE</span>
                                <strong class="text-primary font-monospace d-block fs-5 fw-bold" style="color: var(--color-primary-dark) !important; font-size: 1.1rem !important;"><?= htmlspecialchars($success_data['time_label']) ?></strong>
                            </div>
                        </div>

                        <!-- Ticket Perforation Separator -->
                        <div class="ticket-tear-line"></div>

                        <!-- Attendance warnings text -->
                        <div class="bg-danger-subtle text-danger border border-danger-subtle rounded-3 p-3 mb-4 text-start">
                            <strong class="d-flex align-items-center gap-1 small mb-1" style="font-size: 0.75rem;">
                                <span>⚠️</span> LATENESS POLICY WARNING
                            </strong>
                            <p class="small mb-0 text-dark-emphasis" style="font-size: 0.75rem;">
                                Please arrive on site exactly on time. Delaying your session by <strong>2 to 3 minutes</strong> forfeits the slot automatically to ensure structural scheduling flow.
                            </p>
                        </div>

                        <!-- Screenshot request notification -->
                        <div class="text-center p-3 rounded-pill bg-light border border-dashed text-secondary fw-semibold mb-4" style="font-size: 0.75rem;">
                            📸 Please capture a screenshot of this VIP pass for entry validation!
                        </div>

                        <!-- Virtual Simulated Barcode element -->
                        <div class="ticket-barcode mb-4"></div>

                        <button type="button" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow-sm" style="background: var(--color-bg-dark);" data-bs-dismiss="modal">
                            Screenshot Saved, Dismiss
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php 
        // Instantly destroy success flash records
        unset($_SESSION['booking_success']); 
    ?>
<?php endif; ?>

<!-- Controller Utilities & Animations -->
<script>
    // Open Booking Form Modal & Setup Information
    function openBookingModal(timeValue, timeLabel) {
        document.getElementById('modal_slot_time').value = timeValue;
        document.getElementById('modal_slot_label').value = timeLabel;
        document.getElementById('display_booking_time').innerText = timeLabel;
        
        var modal = new bootstrap.Modal(document.getElementById('bookingModal'));
        modal.show();
    }

    // Auto Dispatch Success Screen & Center active date scroll strip
    window.addEventListener('DOMContentLoaded', (event) => {
        var successModalEl = document.getElementById('successModal');
        if (successModalEl) {
            var successModal = new bootstrap.Modal(successModalEl);
            successModal.show();
        }

        // Center scroll horizontally to keep selected active pill in view on mobile devices
        var activePill = document.querySelector('.date-pill.active');
        if (activePill) {
            var scroller = document.querySelector('.date-scroller');
            scroller.scrollLeft = activePill.offsetLeft - (scroller.clientWidth / 2) + (activePill.clientWidth / 2);
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>