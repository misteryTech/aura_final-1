<?php
require_once '../database/connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$school_id = $data['school_id'] ?? '';

$stmt = $conn->prepare("
   SELECT student_id FROM student_biometrics WHERE student_id = ?

");
$stmt->bind_param("s", $school_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "No biometric record found."
    ]);
    exit;
}

echo json_encode(["success" => true]);
