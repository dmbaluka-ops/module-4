<?php
require_once "db.php";

function safe($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid appointment ID.");
}

$stmt = $conn->prepare(
    "SELECT id, patient_id, doctor_id, appointment_date, reason
     FROM appointments WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$appointment = $result->fetch_assoc();
$stmt->close();

if (!$appointment) {
    exit("Appointment not found.");
}

$patients = $conn->query(
    "SELECT id, fname, lname FROM patients ORDER BY fname, lname"
);

$doctors = $conn->query(
    "SELECT id, fullname, specialisation FROM doctors ORDER BY fullname"
);

if (!$patients || !$doctors) {
    exit("Could not load patients or doctors.");
}

$appointment["appointment_date"] = str_replace(
    " ",
    "T",
    substr($appointment["appointment_date"], 0, 16)
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
</head>
<body>
    <h1>Edit Appointment</h1>

    <form action="update_appointment.php" method="POST">
        <input type="hidden" name="id" value="<?= (int)$appointment["id"] ?>">

        <label for="patient_id">Patient:</label>
        <select id="patient_id" name="patient_id" required>
            <?php while ($patient = $patients->fetch_assoc()): ?>
                <option value="<?= (int)$patient["id"] ?>"
                    <?= (int)$appointment["patient_id"] === (int)$patient["id"] ? "selected" : "" ?>>
                    <?= safe($patient["fname"] . " " . $patient["lname"]) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label for="doctor_id">Doctor:</label>
        <select id="doctor_id" name="doctor_id" required>
            <?php while ($doctor = $doctors->fetch_assoc()): ?>
                <option value="<?= (int)$doctor["id"] ?>"
                    <?= (int)$appointment["doctor_id"] === (int)$doctor["id"] ? "selected" : "" ?>>
                    <?= safe($doctor["fullname"] . " — " . $doctor["specialisation"]) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br><br>

        <label for="appointment_date">Date and Time:</label>
        <input type="datetime-local" id="appointment_date"
               name="appointment_date"
               value="<?= safe($appointment["appointment_date"]) ?>" required>
        <br><br>

        <label for="reason">Reason:</label>
        <textarea id="reason" name="reason" rows="4"><?= safe($appointment["reason"]) ?></textarea>
        <br><br>

        <button type="submit">Save Changes</button>
    </form>

    <p><a href="appointments.php">Back to Appointments</a></p>
</body>
</html>
<?php $conn->close(); ?>