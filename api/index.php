<?php
// Get the requested URI without query string
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

$path = ltrim($path, '/');

if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/i', $path)) {
    $file = __DIR__ . '/../public/' . $path;
    if (file_exists($file) && is_file($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mime = [
            'css'  => 'text/css',
            'js'   => 'application/javascript',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'ico'  => 'image/x-icon',
            'svg'  => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2'=> 'font/woff2',
            'ttf'  => 'font/ttf',
            'eot'  => 'application/vnd.ms-fontobject',
        ];
        header('Content-Type: ' . ($mime[$ext] ?? 'application/octet-stream'));
        readfile($file);
        exit;
    }
}

// --- 2. Route to PHP files inside public/ ---
// If the path is empty or just "/", serve index.php (or you can redirect to cars.php)
if ($path === '' || $path === '/') {
    $path = 'index.php';
}

// Map the request to a file in the public folder
$target = __DIR__ . '/../public/' . $path;

// Check if the target exists and is a PHP file
if (file_exists($target) && is_file($target)) {
    if (pathinfo($target, PATHINFO_EXTENSION) === 'php') {
        // Include the PHP file – this will execute it
        require $target;
    } else {
        // If it's not a PHP file but exists, serve it directly
        readfile($target);
    }
} else {
    // 404 Not Found
    http_response_code(404);
    echo '<h1>404 - Page Not Found</h1>';
    echo '<p>The requested URL was not found on this server.</p>';
}