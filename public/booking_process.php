<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
requireCustomer(); // Only customers can book

$car_id = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 0;
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$days = isset($_POST['days']) ? (int)$_POST['days'] : 0;

$error = '';

if ($car_id <= 0 || empty($start_date) || $days <= 0) {
    $error = 'Invalid booking data.';
} else {
    // Get car details
    $stmt = $conn->prepare("SELECT rent_per_day FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    $stmt->close();

    if (!$car) {
        $error = 'Car not found.';
    } else {
        $total_cost = $car['rent_per_day'] * $days;

        // Check for overlapping booking (same car, same start date)
        $stmt = $conn->prepare("SELECT id FROM bookings WHERE car_id = ? AND start_date = ? AND status = 'active'");
        $stmt->bind_param("is", $car_id, $start_date);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'This car is already booked for the selected start date. Please choose another date.';
        } else {
            // Insert booking
            $stmt = $conn->prepare("INSERT INTO bookings (car_id, customer_id, start_date, days, total_cost) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisid", $car_id, $_SESSION['user_id'], $start_date, $days, $total_cost);
            if ($stmt->execute()) {
                $_SESSION['booking_success'] = "Booking confirmed! Total: $" . number_format($total_cost, 2);
                header('Location: cars.php');
                exit;
            } else {
                $error = 'Booking failed: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// If error, redirect back with message
if ($error) {
    $_SESSION['booking_error'] = $error;
    header('Location: cars.php');
    exit;
}