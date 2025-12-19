<?php
require_once __DIR__ . '/../../../database/connection.php';

$student_id = $_GET['student_id'] ?? '';

$stmt = $conn->prepare("SELECT * FROM user_table WHERE school_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

header("Content-Type: application/json");

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode([]);
}
