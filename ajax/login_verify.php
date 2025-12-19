<?php
session_start();
require_once '../database/connection.php';

$data = json_decode(file_get_contents("php://input"), true);

$school_id   = $data['school_id'];
$pincode     = $data['pincode'];
$facial_data = $data['facial_data'];

/* =========================
   Get Stored Biometrics
========================= */
$stmt = $conn->prepare("
    SELECT face_token, pincode
    FROM student_biometrics
    WHERE student_id = ?
    LIMIT 1
");
$stmt->bind_param("s", $school_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Student not found"]);
    exit;
}

$row = $result->fetch_assoc();

/* =========================
   FACE VERIFICATION
========================= */
// Detect new face → get token
// Compare with stored face_token
// (reuse your Face++ detect + compare functions)

if ($faceConfidence < 80) {
    echo json_encode([
        "success" => false,
        "message" => "Face verification failed"
    ]);
    exit;
}

/* =========================
   PIN VERIFICATION (LAST)
========================= */
if ($pincode !== $row['pincode']) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid PIN"
    ]);
    exit;
}

/* =========================
   LOGIN SUCCESS
========================= */
$_SESSION['user_id']  = $school_id;
$_SESSION['position'] = $row['role'];

echo json_encode([
    "success" => true,
    "redirect" => "member/dashboard.php"
]);
