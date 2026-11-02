<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

$patient_id = filter_input(INPUT_POST, "patient_id", FILTER_VALIDATE_INT);
$doctor_id = filter_input(INPUT_POST, "doctor_id", FILTER_VALIDATE_INT);
$appointment_date = trim($_POST["appointment_date"] ?? "");
$reason = trim($_POST["reason"] ?? "");

$date = DateTime::createFromFormat("Y-m-d\TH:i", $appointment_date);
$valid_date = $date && $date->format("Y-m-d\TH:i") === $appointment_date;

if (
    !$patient_id || $patient_id < 1 ||
    !$doctor_id || $doctor_id < 1 ||
    !$valid_date
) {
    exit("Please select a valid patient, doctor, date and time.");
}

$appointment_date = $date->format("Y-m-d H:i:s");

$stmt = $conn->prepare(
    "INSERT INTO appointments
     (patient_id, doctor_id, appointment_date, reason)
     VALUES (?, ?, ?, ?)"
);

$stmt->bind_param(
    "iiss",
    $patient_id,
    $doctor_id,
    $appointment_date,
    $reason
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    header("Location: appointments.php");
    exit;
}

$stmt->close();
$conn->close();

exit("Could not book the appointment. Check that the selected patient and doctor exist.");
?>