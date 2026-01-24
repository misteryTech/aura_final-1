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

/* =========================
   Validate PIN
========================= */
$stmt = $conn->prepare("
    SELECT pincode
    FROM student_biometrics
    WHERE student_id = ?
    ORDER BY id DESC
    LIMIT 1
");
$stmt->bind_param("s", $school_id);
$stmt->execute();
$stmt->bind_result($storedPin);
$stmt->fetch();
$stmt->close();

if ($pincode !== $storedPin) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid PIN'
    ]);
    exit;
}

/* =========================
   Attendance Logic
========================= */
date_default_timezone_set('Asia/Manila');

$today = date('Y-m-d');
$currentTime = date('H:i:s');

// Check if attendance exists today
$checkStmt = $conn->prepare("
    SELECT id, time_in, time_out
    FROM attendance
    WHERE student_id = ? AND attendance_date = ?
");
$checkStmt->bind_param("ss", $school_id, $today);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {

    // TIME IN
    $insertStmt = $conn->prepare("
        INSERT INTO attendance (student_id, attendance_date, time_in, status)
        VALUES (?, ?, ?, 'IN')
    ");
    $insertStmt->bind_param("sss", $school_id, $today, $currentTime);
    $insertStmt->execute();
    $insertStmt->close();

    $_SESSION['user_id'] = $school_id;

    echo json_encode([
        'success' => true,
        'type' => 'IN',
        'message' => 'Time In recorded successfully'
    ]);
    exit;

} else {

    $row = $result->fetch_assoc();

    // If already timed out
    if (!empty($row['time_out'])) {
        echo json_encode([
            'success' => false,
            'message' => 'You already timed in and out today'
        ]);
        exit;
    }

    // TIME OUT
    $updateStmt = $conn->prepare("
        UPDATE attendance
        SET time_out = ?, status = 'OUT'
        WHERE id = ?
    ");
    $updateStmt->bind_param("si", $currentTime, $row['id']);
    $updateStmt->execute();
    $updateStmt->close();

    echo json_encode([
        'success' => true,
        'type' => 'OUT',
        'message' => 'Time Out recorded successfully'
    ]);
    exit;
}
