<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireAgency();

$agency_id = $_SESSION['user_id'];

// Fetch all cars of this agency
$stmt = $conn->prepare("SELECT * FROM cars WHERE agency_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$cars = $stmt->get_result();
$stmt->close();

include __DIR__ . '/../includes/header.php';
?>

<h2>My Cars & Bookings</h2>

<?php if ($cars->num_rows == 0): ?>
    <div class="alert alert-info">You haven't added any cars yet. <a href="add_car.php">Add one now</a>.</div>
<?php else: ?>
    <?php while ($car = $cars->fetch_assoc()): ?>
        <div class="card mb-4">
            <div class="card-header">
                <strong><?php echo htmlspecialchars($car['vehicle_model']); ?></strong> 
                (<?php echo htmlspecialchars($car['vehicle_number']); ?>) - $<?php echo number_format($car['rent_per_day'], 2); ?>/day
            </div>
            <div class="card-body">
                <h5>Bookings</h5>
                <?php
                // Fetch bookings for this car
                $stmt2 = $conn->prepare("
                    SELECT b.*, u.name AS customer_name, u.email AS customer_email
                    FROM bookings b
                    JOIN users u ON b.customer_id = u.id
                    WHERE b.car_id = ? AND b.status = 'active'
                    ORDER BY b.start_date DESC
                ");
                $stmt2->bind_param("i", $car['id']);
                $stmt2->execute();
                $bookings = $stmt2->get_result();
                $stmt2->close();

                if ($bookings->num_rows == 0):
                ?>
                    <p class="text-muted">No bookings yet.</p>
                <?php else: ?>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Start Date</th>
                                <th>Days</th>
                                <th>Total Cost</th>
                                <th>Booking Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $bookings->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['customer_email']); ?></td>
                                    <td><?php echo $booking['start_date']; ?></td>
                                    <td><?php echo $booking['days']; ?></td>
                                    <td>$<?php echo number_format($booking['total_cost'], 2); ?></td>
                                    <td><?php echo $booking['booking_date']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>