<?php
header('Content-Type: application/json');
include('../../../database/connection.php');

$query = "
    SELECT COUNT(*) AS total
    FROM user_table
    WHERE account = 'Regular' AND position = 'member'
";

$result = $conn->query($query);
$row = $result->fetch_assoc();

echo json_encode([
    "total" => (int)$row['total']
]);
