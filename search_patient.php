
<?php
require_once "db.php";

// Get the search term from the dashboard.
$search = trim($_GET["q"] ?? "");

$patients = null;
$error = "";

// Only search if something was entered.
if ($search === "") {
    $error = "Please enter a patient ID or name.";
} else {

    // Check whether the search term is a numeric ID.
    $idSearch = ctype_digit($search) ? (int)$search : -1;

    // Search by patient ID, first name, last name or full name.
    $sql = "SELECT * FROM patients
            WHERE id = ?
            OR fname LIKE ?
            OR lname LIKE ?
            OR CONCAT(fname, ' ', lname) LIKE ?
            ORDER BY id DESC";

    $stmt = $conn->prepare($sql);

    if ($stmt) {

        $searchTerm = "%" . $search . "%";

        $stmt->bind_param(
            "isss",
            $idSearch,
            $searchTerm,
            $searchTerm,
            $searchTerm
        );

        $stmt->execute();

        $patients = $stmt->get_result();

    } else {
        $error = "Unable to perform the search. Please try again.";
    }
}

// Safely display database information.
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

    <title>Search Patients | Tibabu Hospital</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 35px;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #263238;
        }

        .container {
            max-width: 1000px;
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

        .search-card,
        .results-card {
            background: white;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 10px;
            border: 1px solid #e7edf5;
        }

        .search-form {
            display: flex;
            gap: 10px;
        }

        input {
            flex: 1;
            min-width: 0;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }

        button {
            padding: 12px 20px;
            background: #b22222;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #8b0000;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #edf1f7;
            text-align: left;
        }

        th {
            background: #f8fafc;
            color: #475569;
        }

        .view-link {
            color: #2563eb;
            font-weight: bold;
        }

        .message {
            padding: 15px;
            background: #fff1f2;
            color: #9f1239;
            border-radius: 6px;
        }

        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #2563eb;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            .search-form {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>TIBABU HOSPITAL</h1>

    <p class="subtitle">Patient Search Results</p>

    <div class="search-card">

        <h3>Search for a Patient</h3>

        <form method="GET" action="search_patient.php"
              class="search-form">

            <input
                type="text"
                name="q"
                placeholder="Enter patient ID or name"
                value="<?= safe($search) ?>"
                required
            >

            <button type="submit">Search</button>

        </form>

    </div>

    <div class="results-card">

        <h3>Search Results</h3>

        <?php if ($error !== ""): ?>

            <p class="message"><?= safe($error) ?></p>

        <?php elseif ($patients && $patients->num_rows > 0): ?>

            <p>
                Found <?= $patients->num_rows ?> matching patient(s).
            </p>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Gender</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($patient = $patients->fetch_assoc()): ?>

                        <tr>

                            <td><?= safe($patient["id"]) ?></td>

                            <td><?= safe($patient["fname"]) ?></td>

                            <td><?= safe($patient["lname"]) ?></td>

                            <td><?= safe($patient["gender"]) ?></td>

                            <td><?= safe($patient["contactno"]) ?></td>

                            <td>
                                <a
                                    class="view-link"
                                    href="read.php?id=<?= (int)$patient["id"] ?>"
                                >
                                    View Details
                                </a>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <p class="message">
                No patients found matching your search.
            </p>

        <?php endif; ?>

    </div>

    <a href="index.php" class="back-link">
        ← Back to Dashboard
    </a>

</div>

</body>
</html>

<?php
if (isset($stmt)) {
    $stmt->close();
}

$conn->close();
?>
