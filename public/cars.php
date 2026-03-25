<?php
require_once 'config/config.php';
require_once 'includes/auth.php';

// Fetch all cars with their agency name
$sql = "SELECT c.*, u.name AS agency_name 
        FROM cars c 
        JOIN users u ON c.agency_id = u.id 
        ORDER BY c.id DESC";
$result = $conn->query($sql);

include 'includes/header.php';
?>
<?php if (isset($_SESSION['booking_success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['booking_success']; unset($_SESSION['booking_success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['booking_error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['booking_error']; unset($_SESSION['booking_error']); ?></div>
<?php endif; ?>

<h2>Available Cars for Rent</h2>

<?php if ($result->num_rows == 0): ?>
    <div class="alert alert-info">No cars available at the moment.</div>
<?php else: ?>
    <div class="row">
        <?php while ($car = $result->fetch_assoc()): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($car['vehicle_model']); ?></h5>
                        <p class="card-text">
                            <strong>Vehicle #:</strong> <?php echo htmlspecialchars($car['vehicle_number']); ?><br>
                            <strong>Seats:</strong> <?php echo $car['seating_capacity']; ?><br>
                            <strong>Rent/Day:</strong> $<?php echo number_format($car['rent_per_day'], 2); ?><br>
                            <strong>Agency:</strong> <?php echo htmlspecialchars($car['agency_name']); ?>
                        </p>

                        <?php if (isCustomer()): ?>
                            <!-- Booking form for logged-in customers -->
                            <form action="booking_process.php" method="POST" class="mt-3">
                                <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                                <div class="mb-2">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Number of Days</label>
                                    <select name="days" class="form-select" required>
                                        <?php for ($i = 1; $i <= 30; $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> day(s)</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Rent Car</button>
                            </form>
                        <?php elseif (isAgency()): ?>
                            <!-- Agency can see edit button for their own cars -->
                            <?php if ($car['agency_id'] == $_SESSION['user_id']): ?>
                                <a href="agency/edit_car.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Guest: no booking controls, just info -->
                            <p class="text-muted mt-3">Please <a href="login.php">login as customer</a> to book this car.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>