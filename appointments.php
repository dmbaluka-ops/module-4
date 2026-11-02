<?php
require_once "db.php";

$sql = "SELECT
            a.id,
            a.patient_id,
            a.doctor_id,
            a.appointment_date,
            a.reason,
            p.fname,
            p.lname,
            d.fullname,
            d.specialisation
        FROM appointments a
        INNER JOIN patients p ON a.patient_id = p.id
        INNER JOIN doctors d ON a.doctor_id = d.id
        ORDER BY a.appointment_date DESC";

$result = $conn->query($sql);

function safe($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
</head>
<body>
    <h1>Hospital Management System</h1>
    <h2>Appointments</h2>

    <p><a href="book_appointment.php">Book New Appointment</a></p>
    <p><a href="index.php">Home</a></p>

    <?php if ($result && $result->num_rows > 0): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Specialisation</th>
                    <th>Date and Time</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($appointment = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int)$appointment["id"] ?></td>
                        <td><?= safe($appointment["fname"] . " " . $appointment["lname"]) ?></td>
                        <td><?= safe($appointment["fullname"]) ?></td>
                        <td><?= safe($appointment["specialisation"]) ?></td>
                        <td><?= safe($appointment["appointment_date"]) ?></td>
                        <td><?= safe($appointment["reason"]) ?></td>
                        <td>
                            <a href="edit_appointment.php?id=<?= (int)$appointment["id"] ?>">
                                Edit
                            </a>

                            <form action="delete_appointment.php" method="POST"
                                  onsubmit="return confirm('Cancel this appointment?');"
                                  style="display:inline;">
                                <input type="hidden" name="id"
                                       value="<?= (int)$appointment["id"] ?>">
                                <button type="submit">Cancel</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No appointments have been booked yet.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>