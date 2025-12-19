<?php
session_start();
require_once __DIR__ . '/../../../database/connection.php';

// Fetch all student requests
$query = "SELECT * FROM student_request ORDER BY date_request DESC";
$result = $conn->query($query);

$requests = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($requests);

$conn->close();
