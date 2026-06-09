<?php require_once __DIR__ . '/../includes/header.php'; ?>

<!-- Date Selector Header Section -->
<div class="row justify-content-center mb-5">
    <div class="col-md-6 text-center">
        <h1 class="fw-bold text-dark mb-3">Book Your Barber Session</h1>
        <p class="text-muted">Select a date below to view available time slots.</p>
        
        <form method="GET" action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/" class="d-flex justify-content-center gap-2">
            <input type="date" 
                   class="form-control form-control-lg text-center fw-semibold shadow-sm w-auto" 
                   id="selected_date_picker" 
                   name="date" 
                   value="<?= $selected_date ?>" 
                   min="<?= date('Y-m-d') ?>"
                   onchange="this.form.submit()">
        </form>
    </div>
</div>

<!-- Time Slot Cards Grid -->
<h3 class="text-secondary fw-semibold border-bottom pb-2 mb-4">
    💈 Slots for <?= date('F d, Y', strtotime($selected_date)) ?>
</h3>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($time_slots as $slot): ?>
        <?php 
            // Match slot key format (HH:MM) to verify if booked
            $slot_hour_min = substr($slot['start'], 0, 5);
            $is_booked = in_array($slot_hour_min, $booked_times);
        ?>
        <div class="col">
            <div class="card h-100 shadow-sm border-0 transition-all <?= $is_booked ? 'bg-light opacity-75' : 'border-top border-primary border-4' ?>">
                <div class="card-body d-flex flex-column justify-content-between p-4">
                    <div class="text-center mb-3">
                        <span class="fs-4 text-dark font-monospace fw-bold d-block mb-1">
                            <?= htmlspecialchars($slot['label']) ?>
                        </span>
                        <span class="badge rounded-pill <?= $is_booked ? 'bg-danger' : 'bg-success' ?>">
                            <?= $is_booked ? 'Occupied' : 'Available' ?>
                        </span>
                    </div>

                    <?php if ($is_booked): ?>
                        <button class="btn btn-secondary w-100 py-2" disabled>Slot Booked</button>
                    <?php else: ?>
                        <button class="btn btn-outline-primary w-100 py-2 fw-semibold"
                                onclick="openBookingModal('<?= $slot['start'] ?>', '<?= $slot['label'] ?>')">
                            Book This Time
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- ================= MODALS SECTION ================= -->

<!-- 1. Booking Form Input Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="bookingModalLabel">Confirm Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/book" method="POST">
                <!-- CSRF & Slot Identification Hidden Inputs -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="appointment_date" value="<?= $selected_date ?>">
                <input type="hidden" name="appointment_time" id="modal_slot_time">
                <input type="hidden" name="appointment_time_label" id="modal_slot_label">

                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 text-center mb-4 py-2">
                        Booking for: <strong id="display_booking_time" class="fs-5 text-dark font-monospace"></strong> on <strong class="text-dark"><?= date('M d, Y', strtotime($selected_date)) ?></strong>
                    </div>

                    <div class="mb-3">
                        <label for="customer_name" class="form-label fw-semibold">Full Name</label>
                        <input type="text" class="form-control form-control-lg" id="customer_name" name="customer_name" placeholder="John Doe" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                        <input type="tel" class="form-control form-control-lg" id="phone" name="phone" placeholder="0917XXXXXXX" required>
                    </div>

                    <div class="mb-3">
                        <label for="service" class="form-label fw-semibold">Service Choice</label>
                        <select class="form-select form-select-lg" id="service" name="service" required>
                            <option value="Classic Haircut">Classic Haircut</option>
                            <option value="Beard Grooming">Beard Grooming</option>
                            <option value="Combo: Cut & Beard">Combo: Cut & Beard</option>
                            <option value="Hot Towel Treatment">Hot Towel Treatment</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Complete Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Success Ticket Modal (With Warnings & Screenshot request) -->
<?php if (isset($_SESSION['booking_success'])): ?>
    <?php $success_data = $_SESSION['booking_success']; ?>
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-top border-success border-5 shadow-lg">
                <div class="modal-body text-center p-5">
                    <div class="mb-3 text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-patch-check-fill" viewBox="0 0 16 16">
                            <path d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01-.622-.636zm.287 5.984-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708.708z"/>
                        </svg>
                    </div>
                    <h3 class="fw-bold text-success mb-4">Appointment Booked!</h3>
                    
                    <div class="bg-light p-3 rounded text-start mb-4 border border-dashed">
                        <p class="mb-2 text-muted">Ticket Owner: <span class="text-dark fw-bold"><?= htmlspecialchars($success_data['name']) ?></span></p>
                        <p class="mb-2 text-muted">Service: <span class="text-dark fw-bold"><?= htmlspecialchars($success_data['service']) ?></span></p>
                        <p class="mb-2 text-muted">Date: <span class="text-dark fw-bold"><?= htmlspecialchars($success_data['date']) ?></span></p>
                        <p class="mb-0 text-muted">Scheduled Time: <span class="text-primary fw-bold font-monospace fs-5"><?= htmlspecialchars($success_data['time_label']) ?></span></p>
                    </div>

                    <!-- Critical Warning & Instructive Info -->
                    <div class="alert alert-warning border-0 p-3 mb-4 text-start">
                        <p class="fw-bold mb-1 text-danger">⚠️ Lateness Notice:</p>
                        <p class="small mb-0 text-dark">Please arrive at the shop on time. Being <strong>2-3 minutes late</strong> will result in another customer taking your reserved time slot.</p>
                    </div>

                    <div class="alert alert-secondary border-0 p-2 text-center">
                        <small class="fw-bold text-muted">📸 Tip: Please screenshot this modal for your reference!</small>
                    </div>

                    <button type="button" class="btn btn-success px-5 py-2 fw-semibold w-100 shadow" data-bs-dismiss="modal">I've screenshotted, close</button>
                </div>
            </div>
        </div>
    </div>
    <?php 
        // Remove the flash data so the success modal doesn't show up again on refresh
        unset($_SESSION['booking_success']); 
    ?>
<?php endif; ?>

<!-- Core Logic Utilities -->
<script>
    // Open Booking Form Modal & Prepopulate Hidden values
    function openBookingModal(timeValue, timeLabel) {
        document.getElementById('modal_slot_time').value = timeValue;
        document.getElementById('modal_slot_label').value = timeLabel;
        document.getElementById('display_booking_time').innerText = timeLabel;
        
        var modal = new bootstrap.Modal(document.getElementById('bookingModal'));
        modal.show();
    }

    // Auto load the success modal if it is rendered on the page
    window.addEventListener('DOMContentLoaded', (event) => {
        var successModalEl = document.getElementById('successModal');
        if (successModalEl) {
            var successModal = new bootstrap.Modal(successModalEl);
            successModal.show();
        }
    });
</script>

<style>
    /* Premium Hover Animation Effects for Slots */
    .transition-all {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-all:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.15)!important;
    }
    .border-dashed {
        border: 2px dashed #dee2e6;
    }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>