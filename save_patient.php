
<?php
require_once "db.php";

// Accept only form submissions.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Please submit the patient registration form.");
}

// Receive and clean the submitted data.
$fname = trim($_POST["fname"] ?? "");
$lname = trim($_POST["lname"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$dob = trim($_POST["dob"] ?? "");
$contactno = trim($_POST["contactno"] ?? "");
$email = trim($_POST["email"] ?? "");
$address = trim($_POST["address"] ?? "");

// Check required fields.
if (
    $fname === "" ||
    $lname === "" ||
    $gender === "" ||
    $dob === "" ||
    $contactno === "" ||
    $address === ""
) {
    http_response_code(400);
    exit("Please fill in all required fields.");
}

// Check the length of names.
if (
    strlen($fname) > 50 ||
    strlen($lname) > 50
) {
    http_response_code(400);
    exit("Names must not exceed 50 characters.");
}

// Validate gender.
if (!in_array($gender, ["Male", "Female", "Other"], true)) {
    http_response_code(400);
    exit("Invalid gender selection.");
}

// Validate date of birth.
$date = DateTime::createFromFormat("!Y-m-d", $dob);
$dateErrors = DateTime::getLastErrors();

if (
    !$date ||
    ($dateErrors !== false &&
     ($dateErrors["warning_count"] > 0 ||
      $dateErrors["error_count"] > 0)) ||
    $date->format("Y-m-d") !== $dob ||
    $dob > date("Y-m-d")
) {
    http_response_code(400);
    exit("Please enter a valid date of birth that is not in the future.");
}

// Validate contact number.
// Allow digits only, with a maximum of 10 digits to match
// the current database column's VARCHAR(10) capacity.
if (!preg_match('/^[0-9]{7,10}$/', $contactno)) {
    http_response_code(400);
    exit("Enter a contact number containing 7 to 10 digits.");
}

// Validate optional email.
if (
    $email !== "" &&
    (!filter_var($email, FILTER_VALIDATE_EMAIL) ||
     strlen($email) > 100)
) {
    http_response_code(400);
    exit("Please enter a valid email address of no more than 100 characters.");
}

// Check address length against the database column.
if (strlen($address) > 65535) {
    http_response_code(400);
    exit("The address is too long.");
}

// Prepare the database query.
$sql = "INSERT INTO patients
        (fname, lname, gender, dob, contactno, email, address)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Patient query preparation failed: " . $conn->error);
    http_response_code(500);
    exit("Unable to process the registration right now. Please try again.");
}

$stmt->bind_param(
    "sssssss",
    $fname,
    $lname,
    $gender,
    $dob,
    $contactno,
    $email,
    $address
);

// Save the patient.
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    header("Location: patients.php?registered=1");
    exit();
}

error_log("Patient registration failed: " . $stmt->error);

$stmt->close();
$conn->close();

http_response_code(500);
exit("Patient registration failed. Please try again.");
?>


