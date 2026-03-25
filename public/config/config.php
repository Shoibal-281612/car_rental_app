<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = getenv('DB_HOST') ?: 'autorack.proxy.rlwy.net';
$port = getenv('DB_PORT') ?: '24540';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'SdCYuYvIoBsekhSuwCXEmvgPXeQagUCA';
$name = getenv('DB_NAME') ?: 'railway';

$conn = new mysqli($host, $user, $pass, $name, $port);
// Railway requires SSL; this allows any certificate
$conn->ssl_set(NULL, NULL, NULL, NULL, NULL);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host_url = $_SERVER['HTTP_HOST'];
$base_url = $protocol . "://" . $host_url . "/";
define('BASE_URL', $base_url);

require_once __DIR__ . '/../includes/session_handler.php';
$handler = new SessionHandlerDB();
session_set_save_handler($handler, true);
session_start();

require_once __DIR__ . '/../includes/auth.php';
?>