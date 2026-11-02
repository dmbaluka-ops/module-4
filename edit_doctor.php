
<?php
require_once "db.php";

function safe($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid doctor ID.");
}

$sql = "SELECT id, fullname, specialisation, contactno
        FROM doctors
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    exit("Unable to retrieve doctor information.");
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

$stmt->close();
$conn->close();

if (!$doctor) {
    exit("Doctor not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Doctor | Tibabu Hospital</title>

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

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 15px;
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

    <p class="subtitle">Update Doctor Information</p>

    <div class="form-card">

        <form action="update_doctor.php" method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= safe($doctor["id"]) ?>"
            >

            <label for="fullname">Full Name</label>

            <input
                type="text"
                id="fullname"
                name="fullname"
                maxlength="100"
                value="<?= safe($doctor["fullname"]) ?>"
                required
            >

            <label for="specialisation">Specialisation</label>

            <input
                type="text"
                id="specialisation"
                name="specialisation"
                maxlength="100"
                value="<?= safe($doctor["specialisation"]) ?>"
                required
            >

            <label for="contactno">Contact Number</label>

            <input
                type="tel"
                id="contactno"
                name="contactno"
                maxlength="10"
                value="<?= safe($doctor["contactno"]) ?>"
            >

            <button type="submit">
                Save Changes
            </button>

        </form>

    </div>

    <a href="read_doctor.php?id=<?= (int)$doctor["id"] ?>"
       class="back-link">
        ← Back to Doctor Details
    </a>

</div>

</body>
</html>
