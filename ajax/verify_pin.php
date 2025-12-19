<?php
session_start();
include('../database/connection.php');

header("Content-Type: application/json");

// Read JSON input
$input = json_decode(file_get_contents("php://input"), true);

$school_id = $input['school_id'] ?? null;
$pincode   = $input['pincode'] ?? null;

if (!$school_id || !$pincode) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing data'
    ]);
    exit;
}

$stmt = $conn->prepare("SELECT pincode FROM student_biometrics WHERE student_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("s", $school_id);
$stmt->execute();
$stmt->bind_result($storedPin);
$stmt->fetch();
$stmt->close();

if ($pincode === $storedPin) {

    $_SESSION['user_id'] = $school_id;

    echo json_encode([
        'success' => true,
        'message' => 'PIN verified',
        'redirect' => '../member/dashboard.php'
    ]);
    exit;

} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid PIN'
    ]);
    exit;
}
