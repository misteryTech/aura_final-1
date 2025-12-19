<?php
session_start();

header('Content-Type: application/json');
require_once '../../../database/connection.php';

$student_id = $_SESSION['school_id'] ?? null;
$pincode    = $_POST['pincode'] ?? null;

/* =========================
   Validation
========================= */
if (!$student_id) {
    echo json_encode([
        "success" => false,
        "message" => "Missing student_id"
    ]);
    exit;
}

if (!$pincode) {
    echo json_encode([
        "success" => false,
        "message" => "Missing pincode"
    ]);
    exit;
}

/* =========================
   Check existing biometrics
========================= */
$check = $conn->prepare(
    "SELECT id FROM student_biometrics WHERE student_id = ? LIMIT 1"
);
$check->bind_param("s", $student_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {

    // 🔁 UPDATE existing biometrics
    $update = $conn->prepare(
        "UPDATE student_biometrics
         SET pincode = ?
         WHERE student_id = ?"
    );

    if ($update === false) {
        echo json_encode([
            "success" => false,
            "message" => $conn->error
        ]);
        exit;
    }

    $update->bind_param("ss", $pincode, $student_id);

    if ($update->execute()) {
        echo json_encode([
            "success" => true,
            "action"  => "updated",
            "message" => "Biometrics updated successfully!"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => $update->error
        ]);
    }

    $update->close();

} else {

    // ➕ INSERT if not exists (fallback)
    $insert = $conn->prepare(
        "INSERT INTO student_biometrics (pincode, student_id) VALUES (?, ?)"
    );

    if ($insert === false) {
        echo json_encode([
            "success" => false,
            "message" => $conn->error
        ]);
        exit;
    }

    $insert->bind_param("ss", $pincode, $student_id);

    if ($insert->execute()) {
        echo json_encode([
            "success" => true,
            "action"  => "inserted",
            "message" => "Biometrics created successfully!"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => $insert->error
        ]);
    }

    $insert->close();
}

$check->close();
$conn->close();
?>
