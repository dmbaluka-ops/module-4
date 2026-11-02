
<?php
require_once "db.php";

// Only allow deletion through POST.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

// Retrieve and validate doctor ID.
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid doctor ID.");
}

// Delete the selected doctor.
$stmt = $conn->prepare(
    "DELETE FROM doctors WHERE id = ?"
);

if (!$stmt) {
    exit("Could not prepare the delete query.");
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        $stmt->close();
        $conn->close();

        // Return to the doctors page after deletion.
        header("Location: doctors.php");
        exit;
    }

    $stmt->close();
    $conn->close();

    exit("Doctor not found.");
}

$stmt->close();
$conn->close();

exit(
    "Could not delete the doctor. "
    . "The doctor may have existing appointments."
);
?>
