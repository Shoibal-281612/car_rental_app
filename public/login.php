<?php
require_once 'config/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: cars.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Please fill all fields.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, password, user_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_type'] = $row['user_type'];
                header('Location: cars.php');
                exit;
            } else {
                $error = 'Invalid credentials.';
            }
        } else {
            $error = 'User not found.';
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>
<h2>Login</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
<p class="mt-3">Don't have an account? <a href="register_customer.php">Register as Customer</a> or <a href="register_agency.php">Register as Agency</a>.</p>
<?php include 'includes/footer.php'; ?>