<?php
require_once "db.php";

$patients = $conn->query(
    "SELECT id, fname, lname FROM patients ORDER BY fname, lname"
);

$doctors = $conn->query(
    "SELECT id, fullname, specialisation FROM doctors ORDER BY fullname"
);

function safe($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
</head>
<body>
    <h1>Hospital Management System</h1>
    <h2>Book an Appointment</h2>

    <?php if (!$patients || !$doctors): ?>
        <p>Could not load patients or doctors. Please try again.</p>
    <?php elseif ($patients->num_rows === 0 || $doctors->num_rows === 0): ?>
        <p>You must register at least one patient and one doctor first.</p>
    <?php else: ?>
        <form action="save_appointment.php" method="POST">
            <label for="patient_id">Patient:</label>
            <select id="patient_id" name="patient_id" required>
                <option value="">Select Patient</option>
                <?php while ($patient = $patients->fetch_assoc()): ?>
                    <option value="<?= (int)$patient["id"] ?>">
                        <?= safe($patient["fname"] . " " . $patient["lname"]) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br><br>

            <label for="doctor_id">Doctor:</label>
            <select id="doctor_id" name="doctor_id" required>
                <option value="">Select Doctor</option>
                <?php while ($doctor = $doctors->fetch_assoc()): ?>
                    <option value="<?= (int)$doctor["id"] ?>">
                        <?= safe($doctor["fullname"]) ?>
                        — <?= safe($doctor["specialisation"]) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br><br>

            <label for="appointment_date">Date and Time:</label>
            <input type="datetime-local" id="appointment_date"
                   name="appointment_date" required>
            <br><br>

            <label for="reason">Reason for Appointment:</label>
            <textarea id="reason" name="reason" rows="4"></textarea>
            <br><br>

            <button type="submit">Book Appointment</button>
        </form>
    <?php endif; ?>

    <p><a href="appointments.php">View Appointments</a></p>
    <p><a href="index.php">Home</a></p>
</body>
</html>
<?php $conn->close(); ?>