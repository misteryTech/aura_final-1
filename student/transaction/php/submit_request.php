<?php
session_start();
require_once __DIR__ . "/../../../database/connection.php";

// Only allow logged-in students
if (!isset($_SESSION['school_id']) || $_SESSION['position'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get POST data
$student_id = $_POST['student_id'] ?? '';
$remarks    = $_POST['remarks'] ?? '';
$status    = 'Pending';

if (empty($student_id)) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required']);
    exit;
}

// Insert into student_request table
$stmt = $conn->prepare("
    INSERT INTO student_request (student_id, remarks, date_request, status)
    VALUES (?, ?, NOW(), ?)
");

$stmt->bind_param("sss", $student_id, $remarks, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
