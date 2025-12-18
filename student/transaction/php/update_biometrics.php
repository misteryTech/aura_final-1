<?php
session_start();

header('Content-Type: application/json');
include('../../../database/connection.php');

$student_id = $_SESSION['school_id'] ?? null;
$pincode    = $_POST['pincode'] ?? null;

if (!$student_id) {
    echo json_encode(["success" => false, "message" => "Missing student_id"]);
    exit;
}

if (!$pincode) {
    echo json_encode(["success" => false, "message" => "Missing pincode"]);
    exit;
}

// 🔎 Check if biometrics already exist for this student
$check = $conn->prepare("SELECT id FROM student_biometrics WHERE student_id = ?");
$check->bind_param("i", $student_id);
$check->execute();
$checkResult = $check->get_result();

if ($checkResult->num_rows > 0) {
    // Already has biometrics → return reason
    echo json_encode([
        "success" => false,
        "reason"  => "no_data",
        "message" => "Biometrics already exist for this student."
    ]);
    $check->close();
    exit;
}
$check->close();

// ✅ Insert new biometrics
$stmt = $conn->prepare("INSERT INTO student_biometrics (pincode, student_id) VALUES (?, ?)");
if ($stmt === false) {
    echo json_encode(["success" => false, "message" => $conn->error]);
    exit;
}

// pincode = string, student_id = integer
$stmt->bind_param("ss", $pincode, $student_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Biometrics inserted successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>