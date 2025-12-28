<?php
session_start();
require_once __DIR__ . '/../../../database/connection.php';

// Fetch all student requests with fingerprint status
$query = "
    SELECT
        u.id,
        u.school_id,
        u.first_name,
        u.last_name,
        u.date_registration,
        sb.student_id,
        sb.biometrics,
        CASE
            WHEN sb.student_id IS NULL
                THEN 'Not yet requested to biometrics'
            WHEN sb.biometrics IS NULL OR sb.biometrics = ''
                THEN 'No fingerprint registered'
            ELSE 'Has fingerprint'
        END AS status
    FROM user_table u
    LEFT JOIN student_biometrics sb
        ON u.school_id = sb.student_id
    ORDER BY u.date_registration DESC
";

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