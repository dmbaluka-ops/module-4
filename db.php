
<?php
$host = "db";
$username = "hospital_app";
$password = "hospital_app_password";
$database = "hospital_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}

$conn->set_charset("utf8mb4");
?>
