<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Doctor</title>
</head>
<body>

    <h1>Hospital Management System</h1>
    <h2>Register a New Doctor</h2>

    <form action="save_doctor.php" method="POST">

        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname"
               maxlength="100" required>
        <br><br>

        <label for="specialisation">Specialisation:</label>
        <input type="text" id="specialisation" name="specialisation"
               maxlength="100" required>
        <br><br>

        <label for="contactno">Contact Number:</label>
        <input type="tel" id="contactno" name="contactno"
               maxlength="10">
        <br><br>

        <button type="submit">Register Doctor</button>

    </form>

    <p>
        <a href="doctors.php">View Registered Doctors</a>
    </p>

    <p>
        <a href="patients.php">Back to Patients</a>
    </p>

</body>
</html>
