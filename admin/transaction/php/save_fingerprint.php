<?php
require_once __DIR__ . '/../../../database/connection.php';
header('Content-Type: application/json');
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$student_id = $_POST['student_id'] ?? '';
$fingerprintId = $_POST['fingerprintId'] ?? '';


if ($student_id && $fingerprintId) {
    $stmt = $conn->prepare("UPDATE student_biometrics SET biometrics = ? WHERE student_id = ?");
    $stmt->bind_param("ss", $fingerprintId, $student_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Fingerprint updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No record found for this student_id.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update fingerprint.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
}

$conn->close();