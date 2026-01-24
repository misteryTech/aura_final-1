<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../database/connection.php';

$data = json_decode(file_get_contents("php://input"), true);

$school_id = $data['school_id'] ?? '';
$fingerprint_id = $data['fingerprint_id'] ?? '';

if (!$school_id || !$fingerprint_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid fingerprint data'
    ]);
    exit;
}

// Fetch the stored fingerprint ID for this student
$stmt = $conn->prepare("
    SELECT sb.biometrics
    FROM student_biometrics sb
    WHERE sb.student_id = ?
    LIMIT 1
");
$stmt->bind_param("s", $school_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check if fingerprint matches
if (!$row || $row['biometrics'] != $fingerprint_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Fingerprint does not match this account'
    ]);
    exit;
}

// Success
echo json_encode([
    'success' => true,
    'message' => 'Fingerprint matched'
]);
