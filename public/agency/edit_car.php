<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
requireAgency();

$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch car and verify it belongs to this agency
$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ? AND agency_id = ?");
$stmt->bind_param("ii", $car_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();
$stmt->close();

if (!$car) {
    header('Location: ../cars.php');
    exit;
}

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
        $stmt = $conn->prepare("UPDATE cars SET vehicle_model=?, vehicle_number=?, seating_capacity=?, rent_per_day=? WHERE id=? AND agency_id=?");
        $stmt->bind_param("ssidii", $model, $number, $seats, $rent, $car_id, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $success = 'Car updated successfully!';
            // Refresh car data
            $car['vehicle_model'] = $model;
            $car['vehicle_number'] = $number;
            $car['seating_capacity'] = $seats;
            $car['rent_per_day'] = $rent;
        } else {
            $error = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    }
}

include '../includes/header.php';
?>
<h2>Edit Car</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label for="model" class="form-label">Vehicle Model</label>
        <input type="text" class="form-control" id="model" name="model" value="<?php echo htmlspecialchars($car['vehicle_model']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="vehicle_number" class="form-label">Vehicle Number</label>
        <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" value="<?php echo htmlspecialchars($car['vehicle_number']); ?>" required>
    </div>
    <div class="mb-3">
        <label for="seating_capacity" class="form-label">Seating Capacity</label>
        <input type="number" class="form-control" id="seating_capacity" name="seating_capacity" value="<?php echo $car['seating_capacity']; ?>" min="1" required>
    </div>
    <div class="mb-3">
        <label for="rent_per_day" class="form-label">Rent per Day ($)</label>
        <input type="number" step="0.01" class="form-control" id="rent_per_day" name="rent_per_day" value="<?php echo $car['rent_per_day']; ?>" min="0" required>
    </div>
    <button type="submit" class="btn btn-primary">Update Car</button>
    <a href="../cars.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../includes/footer.php'; ?>