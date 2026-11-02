
<?php
require_once "db.php";

function safe($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Get and validate patient ID.
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid patient ID.");
}

// Retrieve individual patient details.
$sql = "SELECT * FROM patients WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    exit("Unable to retrieve patient details.");
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$patient = $result->fetch_assoc();

$stmt->close();
$conn->close();

if (!$patient) {
    exit("Patient not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Patient Details | Tibabu Hospital</title>

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
            max-width: 800px;
            margin: auto;
        }

        h1 {
            color: #b22222;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: #718096;
            margin-bottom: 30px;
        }

        .details-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #e7edf5;
        }

        .detail {
            padding: 14px 0;
            border-bottom: 1px solid #edf1f7;
        }

        .detail strong {
            display: inline-block;
            width: 180px;
            color: #475569;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .edit {
            background: #2563eb;
        }

        .delete {
            background: #dc2626;
        }

        .back {
            background: #64748b;
        }

        .btn:hover {
            opacity: 0.85;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            .detail strong {
                display: block;
                width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>TIBABU HOSPITAL</h1>

    <p class="subtitle">Individual Patient Record</p>

    <div class="details-card">

        <h2>
            <?= safe($patient["fname"]) ?>
            <?= safe($patient["lname"]) ?>
        </h2>

        <div class="detail">
            <strong>Patient ID:</strong>
            <?= safe($patient["id"]) ?>
        </div>

        <div class="detail">
            <strong>First Name:</strong>
            <?= safe($patient["fname"]) ?>
        </div>

        <div class="detail">
            <strong>Last Name:</strong>
            <?= safe($patient["lname"]) ?>
        </div>

        <div class="detail">
            <strong>Gender:</strong>
            <?= safe($patient["gender"]) ?>
        </div>

        <div class="detail">
            <strong>Date of Birth:</strong>
            <?= safe($patient["dob"]) ?>
        </div>

        <div class="detail">
            <strong>Contact Number:</strong>
            <?= safe($patient["contactno"]) ?>
        </div>

        <div class="detail">
            <strong>Email:</strong>
            <?= safe($patient["email"]) ?>
        </div>

        <div class="detail">
            <strong>Address:</strong>
            <?= safe($patient["address"]) ?>
        </div>

        <div class="actions">

            <a
                href="edit_patient.php?id=<?= (int)$patient["id"] ?>"
                class="btn edit"
            >
                Edit Patient
            </a>

            <form
                method="POST"
                action="delete_patient.php"
                onsubmit="return confirm('Are you sure you want to permanently delete this patient?');"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int)$patient["id"] ?>"
                >

                <button type="submit" class="btn delete">
                    Delete Patient
                </button>

            </form>

            <a href="search_patient.php" class="btn back">
                Back to Search
            </a>

        </div>

    </div>

</div>

</body>
</html>


