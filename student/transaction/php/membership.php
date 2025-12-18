<?php
include('../../../database/connection.php');

// Collect form data
$osca_id         = $_POST['osca_id'] ?? '';
$cp_fullname     = $_POST['cp_fullname'] ?? '';
$cp_relationship = $_POST['cp_relationship'] ?? '';
$cp_contact      = $_POST['cp_contact'] ?? '';
$cp_email        = $_POST['cp_email'] ?? '';
$occupation      = $_POST['occupation'] ?? '';
$date_added      = date('Y-m-d H:i:s');

// Prepare SQL Insert
$stmt = $conn->prepare("
    INSERT INTO membership_table
    (osca_id, cp_fullname, cp_relationship, cp_contact, cp_email, cp_occupation, date_added)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssss",
    $osca_id,
    $cp_fullname,
    $cp_relationship,
    $cp_contact,
    $cp_email,
    $occupation,
    $date_added
);

// Execute and respond
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Membership details saved successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
