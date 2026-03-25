<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
requireAgency();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = sanitize($_POST['model']);
    $number = sanitize($_POST['vehicle_number']);
    $seats = (int)$_POST['seating_capacity'];
    $rent = (float)$_POST['rent_per_day'];

    if (empty($model) || empty($number) || $seats <= 0 || $rent <= 0) {
        $error = 'All fields are required and must be valid.';
    } else {
        $agency_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO cars (agency_id, vehicle_model, vehicle_number, seating_capacity, rent_per_day) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issid", $agency_id, $model, $number, $seats, $rent);
        if ($stmt->execute()) {
            $success = 'Car added successfully!';
        } else {
            $error = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    }
}

include '../includes/header.php';
?>
<h2>Add New Car</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label for="model" class="form-label">Vehicle Model</label>
        <input type="text" class="form-control" id="model" name="model" required>
    </div>
    <div class="mb-3">
        <label for="vehicle_number" class="form-label">Vehicle Number</label>
        <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" required>
    </div>
    <div class="mb-3">
        <label for="seating_capacity" class="form-label">Seating Capacity</label>
        <input type="number" class="form-control" id="seating_capacity" name="seating_capacity" min="1" required>
    </div>
    <div class="mb-3">
        <label for="rent_per_day" class="form-label">Rent per Day ($)</label>
        <input type="number" step="0.01" class="form-control" id="rent_per_day" name="rent_per_day" min="0" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Car</button>
    <a href="../cars.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../includes/footer.php'; ?>