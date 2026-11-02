
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
</head>
<body>

    <h1>Hospital Management System</h1>
    <h2>Register a New Patient</h2>

    <form action="save_patient.php" method="POST">

        <label for="fname">First Name:</label>
        <input type="text" id="fname" name="fname" required>
        <br><br>

        <label for="lname">Last Name:</label>
        <input type="text" id="lname" name="lname" required>
        <br><br>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="">Select gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>
        <br><br>

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required>
        <br><br>

        <label for="contactno">Contact Number:</label>
        <input type="tel" id="contactno" name="contactno"
               maxlength="10" required>
        <br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email">
        <br><br>

        <label for="address">Address:</label>
        <textarea id="address" name="address" required></textarea>
        <br><br>

        <button type="submit">Register Patient</button>

    </form>

</body>
</html>
