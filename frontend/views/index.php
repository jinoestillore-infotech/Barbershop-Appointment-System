<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="row">
    <!-- Booking Form Column -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Book an Appointment</h5>
            </div>
            <div class="card-body">
                <!-- Point the action to our /book route -->
                <form action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/book" method="POST">
                    <!-- Secure CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="service" class="form-label">Service</label>
                        <select class="form-select" id="service" name="service" required>
                            <option value="Classic Haircut">Classic Haircut ($25)</option>
                            <option value="Beard Trim">Beard Trim ($15)</option>
                            <option value="Haircut & Beard">Haircut & Beard Trim ($35)</option>
                            <option value="Hot Towel Shave">Hot Towel Shave ($30)</option>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="appointment_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                        </div>
                        <div class="col">
                            <label for="appointment_time" class="form-label">Time</label>
                            <input type="time" class="form-control" id="appointment_time" name="appointment_time" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Confirm Booking</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Appointments List Column -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">Upcoming Schedule</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($appointments)): ?>
                                <?php foreach ($appointments as $apt): ?>
                                <tr>
                                    <td>
                                        <strong><?= date('M d, Y', strtotime($apt['appointment_date'])) ?></strong><br>
                                        <small class="text-muted"><?= date('h:i A', strtotime($apt['appointment_time'])) ?></small>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($apt['customer_name']) ?><br>
                                        <small class="text-muted"><?= htmlspecialchars($apt['phone']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($apt['service']) ?></td>
                                    <td><span class="badge bg-success"><?= htmlspecialchars($apt['status']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No appointments booked yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>