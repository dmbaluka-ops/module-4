
<?php
require_once "db.php";

function safe($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Get and validate doctor ID.
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid doctor ID.");
}

// Retrieve the individual doctor's information.
$sql = "SELECT id, fullname, specialisation, contactno
        FROM doctors
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    exit("Unable to retrieve doctor details.");
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

    <title>Doctor Details | Tibabu Hospital</title>

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

    <p class="subtitle">Individual Doctor Record</p>

    <div class="details-card">

        <h2><?= safe($doctor["fullname"]) ?></h2>

        <div class="detail">
            <strong>Doctor ID:</strong>
            <?= safe($doctor["id"]) ?>
        </div>

        <div class="detail">
            <strong>Full Name:</strong>
            <?= safe($doctor["fullname"]) ?>
        </div>

        <div class="detail">
            <strong>Specialisation:</strong>
            <?= safe($doctor["specialisation"]) ?>
        </div>

        <div class="detail">
            <strong>Contact Number:</strong>
            <?= safe($doctor["contactno"]) ?>
        </div>

        <div class="actions">

            <a
                href="edit_doctor.php?id=<?= (int)$doctor["id"] ?>"
                class="btn edit"
            >
                Edit Doctor
            </a>

            <form
                method="POST"
                action="delete_doctor.php"
                onsubmit="return confirm('Are you sure you want to permanently delete this doctor?');"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int)$doctor["id"] ?>"
                >

                <button type="submit" class="btn delete">
                    Delete Doctor
                </button>

            </form>

            <a href="search_doctor.php" class="btn back">
                Back to Search
            </a>

        </div>

    </div>

</div>

</body>
</html>
