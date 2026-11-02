
<?php
require_once "db.php";

$sql = "SELECT id, fname, lname, gender, dob, contactno,
               email, address, registered_on
        FROM patients
        ORDER BY registered_on DESC";

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
    <title>Registered Patients</title>
</head>
<body>

    <h1>Hospital Management System</h1>
    <h2>Registered Patients</h2>

    <p>
        <a href="register_patient.php">Register New Patient</a>
    </p>

    <?php if ($result && $result->num_rows > 0): ?>

        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Registered On</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($patient = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= safe($patient["id"]) ?></td>
                        <td><?= safe($patient["fname"]) ?></td>
                        <td><?= safe($patient["lname"]) ?></td>
                        <td><?= safe($patient["gender"]) ?></td>
                        <td><?= safe($patient["dob"]) ?></td>
                        <td><?= safe($patient["contactno"]) ?></td>
                        <td><?= safe($patient["email"]) ?></td>
                        <td><?= safe($patient["address"]) ?></td>
                        <td><?= safe($patient["registered_on"]) ?></td>

                        <td>
                            <a href="edit_patient.php?id=<?= (int)$patient["id"] ?>">
                                Edit
                            </a>

                            <form action="delete_patient.php"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this patient?');"
                                  style="display: inline;">

                                <input type="hidden"
                                       name="id"
                                       value="<?= (int)$patient["id"] ?>">

                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php else: ?>

        <p>No patients have been registered yet.</p>

    <?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>
