<?php
session_start();
session_destroy();
header('Location: cars.php');
exit;