<?php
session_start();
require_once __DIR__ . '/../../../database/connection.php';

header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit;
}

// Match input names with form
$student_id = $_POST['student_id'] ?? '';
$facial_data = $_POST['facial_data'] ?? '';

if (empty($student_id) || empty($facial_data)) {
    echo json_encode(["status" => "error", "message" => "Missing data."]);
    exit;
}

// Remove base64 header
$facial_data = preg_replace('/^data:image\/\w+;base64,/', '', $facial_data);
$facial_data = str_replace(' ', '+', $facial_data);
$imageData = base64_decode($facial_data);

if (!$imageData) {
    echo json_encode(["status" => "error", "message" => "Image decode failed."]);
    exit;
}

// Save file
$folder = __DIR__ . "/../../../uploads/facial_images/";
if (!is_dir($folder)) mkdir($folder, 0777, true);

$file_name = "face_" . $student_id . "_" . time() . ".png";
$file_path = $folder . $file_name;

if (file_put_contents($file_path, $imageData) === false) {
    echo json_encode(["status" => "error", "message" => "Failed to save image file."]);
    exit;
}

// Insert/update in DB
$stmt = $conn->prepare("
    INSERT INTO student_biometrics (student_id, facial_url)
    VALUES (?, ?)
    ON DUPLICATE KEY UPDATE facial_url = ?
");
$stmt->bind_param("sss", $student_id, $file_name, $file_name);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Facial biometric updated!",
        "file" => $file_name
    ]);
    exit;
}

echo json_encode([
    "status" => "error",
    "message" => "Database update failed.",
    "error" => $conn->error
]);
exit;
