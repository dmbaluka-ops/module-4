<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid appointment ID.");
}

$stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        $conn->close();

        header("Location: appointments.php");
        exit;
    }

    $stmt->close();
    $conn->close();

    exit("Appointment not found.");
}

$stmt->close();
$conn->close();

exit("Could not cancel the appointment.");
?>