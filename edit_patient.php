

<?php
require_once "db.php";

// Safely display database values.
function safe($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Validate the patient ID from the URL.
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid patient ID.");
}

// Retrieve the patient's existing information.
$sql = "SELECT id, fname, lname, gender, dob,
               contactno, email, address
        FROM patients
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    exit("Unable to retrieve patient information.");
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$patient = $result->fetch_assoc();

$stmt->close();

if (!$patient) {
    exit("Patient not found.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Patient | Tibabu Hospital</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #263238;
        }

        .container {
            max-width: 700px;
            margin: auto;
        }

        h1 {
            text-align: center;
            color: #b22222;
        }

        .subtitle {
            text-align: center;
            color: #718096;
            margin-bottom: 25px;
        }

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #e7edf5;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        button {
            background: #b22222;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #8b0000;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>TIBABU HOSPITAL</h1>

    <p class="subtitle">Update Patient Information</p>

    <div class="form-card">

        <form action="update_patient.php" method="POST">

            <!-- Identify which patient is being updated. -->
            <input
                type="hidden"
                name="id"
                value="<?= safe($patient['id']) ?>"
            >

            <label for="fname">First Name</label>
            <input
                type="text"
                id="fname"
                name="fname"
                maxlength="50"
                value="<?= safe($patient['fname']) ?>"
                required
            >

            <label for="lname">Last Name</label>
            <input
                type="text"
                id="lname"
                name="lname"
                maxlength="50"
                value="<?= safe($patient['lname']) ?>"
                required
            >

            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <?php foreach (["Male", "Female", "Other"] as $option): ?>
                    <option
                        value="<?= safe($option) ?>"
                        <?= $patient['gender'] === $option ? "selected" : "" ?>
                    >
                        <?= safe($option) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="dob">Date of Birth</label>
            <input
                type="date"
                id="dob"
                name="dob"
                max="<?= date('Y-m-d') ?>"
                value="<?= safe($patient['dob']) ?>"
                required
            >

            <label for="contactno">Contact Number</label>
            <input
                type="tel"
                id="contactno"
                name="contactno"
                maxlength="10"
                value="<?= safe($patient['contactno']) ?>"
                required
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="100"
                value="<?= safe($patient['email']) ?>"
            >

            <label for="address">Address</label>
            <textarea
                id="address"
                name="address"
                required
            ><?= safe($patient['address']) ?></textarea>

            <button type="submit">
                Save Changes
            </button>

        </form>

    </div>

    <a href="search_patient.php" class="back-link">
        ← Back to Patient Search
    </a>

</div>

</body>
</html>

