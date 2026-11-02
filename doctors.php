<?php
require_once "db.php";

$result = $conn->query(
    "SELECT id, fullname, specialisation, contactno
     FROM doctors ORDER BY fullname ASC"
);

function safe($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Doctors</title>
</head>
<body>
    <h1>Hospital Management System</h1>
    <h2>Registered Doctors</h2>

    <p><a href="register_doctor.php">Register New Doctor</a></p>
    <p><a href="index.php">Home</a></p>

    <?php if ($result && $result->num_rows > 0): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Specialisation</th>
                    <th>Contact Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($doctor = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= safe($doctor["id"]) ?></td>
                        <td><?= safe($doctor["fullname"]) ?></td>
                        <td><?= safe($doctor["specialisation"]) ?></td>
                        <td><?= safe($doctor["contactno"]) ?></td>
                        <td>
                            <a href="edit_doctor.php?id=<?= (int)$doctor["id"] ?>">
                                Edit
                            </a>

                            <form action="delete_doctor.php" method="POST"
                                  onsubmit="return confirm('Delete this doctor?');"
                                  style="display:inline;">
                                <input type="hidden" name="id"
                                       value="<?= (int)$doctor["id"] ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No doctors have been registered yet.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>