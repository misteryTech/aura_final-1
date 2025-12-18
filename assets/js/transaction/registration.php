<?php
include('../../../database/connection.php');
// Collect form data
$school_id   = $_POST['school_id'] ?? '';
$first_name  = $_POST['first_name'] ?? '';
$last_name   = $_POST['last_name'] ?? '';
$birth_date  = $_POST['birth_date'] ?? '';
$place_birth = $_POST['place_birth'] ?? '';
$password    = $_POST['password'] ?? '';
$chapter     = $_POST['chapter'] ?? '';
$position    = "student";
$date_added  = date("Y-m-d H:i:s"); // define timestamp

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare insert (only the fields you collect)
$stmt = $conn->prepare("
    INSERT INTO user_table
    (school_id, first_name, last_name, birth_date, place_birth, password, position, date_registration)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssssss",
    $school_id,
    $first_name,
    $last_name,
    $birth_date,
    $place_birth,
    $hashedPassword,
    $position,
    $date_added
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registration successful!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>