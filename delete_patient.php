<?php
require_once "db.php";

// Only allow deletion through a POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

// Get and validate the patient ID
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid patient ID.");
}

// Delete the patient using a prepared statement
$stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");

if (!$stmt) {
    exit("Could not prepare the delete query.");
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        $conn->close();

        header("Location: patients.php");
        exit;
    } else {
        $stmt->close();
        $conn->close();

        exit("Patient not found.");
    }
} else {
    $stmt->close();
    $conn->close();

    exit("Could not delete the patient. They may have linked appointments.");
}
?>
