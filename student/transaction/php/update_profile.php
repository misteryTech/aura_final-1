<?php

session_start();
header('Content-Type: application/json');


require_once __DIR__ . '/../../../database/connection.php';
// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Collect POST data safely
$osca_id = $_POST['osca_id'] ?? '';
$chapter = $_POST['chapter'] ?? '';
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$birth_date = $_POST['birth_date'] ?? '';
$place_birth = $_POST['place_birth'] ?? '';
$civil_status = $_POST['civil_status'] ?? '';
$pensioner = $_POST['pensioner'] ?? '';
$pension_details = $_POST['pension_details'] ?? '';
$email = $_POST['email'] ?? '';

// Validate required fields
if (empty($osca_id) || empty($first_name) || empty($last_name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
    exit;
}

// Prepare update statement
$stmt = $conn->prepare("UPDATE user_table
    SET chapter=?, first_name=?, last_name=?, birth_date=?, place_birth=?,
        civil_status=?, pensioner=?, pension_details=?, email=?
    WHERE osca_id=?");

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: '.$conn->error]);
    exit;
}

$stmt->bind_param(
    "ssssssssss",
    $chapter,
    $first_name,
    $last_name,
    $birth_date,
    $place_birth,
    $civil_status,
    $pensioner,
    $pension_details,
    $email,
    $osca_id
);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed: '.$stmt->error]);
}

$stmt->close();
$conn->close();
exit; // ensure nothing else is output
