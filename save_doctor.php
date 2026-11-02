<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

$fullname = trim($_POST["fullname"] ?? "");
$specialisation = trim($_POST["specialisation"] ?? "");
$contactno = trim($_POST["contactno"] ?? "");

if ($fullname === "" || $specialisation === "") {
    exit("Full name and specialisation are required.");
}

if (strlen($fullname) > 100 || strlen($specialisation) > 100) {
    exit("One of the fields is too long.");
}

if (strlen($contactno) > 10) {
    exit("Contact number must not exceed 10 characters.");
}

$stmt = $conn->prepare(
    "INSERT INTO doctors (fullname, specialisation, contactno)
     VALUES (?, ?, ?)"
);

$stmt->bind_param("sss", $fullname, $specialisation, $contactno);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    header("Location: doctors.php");
    exit;
}

$stmt->close();
$conn->close();

exit("Could not register the doctor. Please try again.");
?>