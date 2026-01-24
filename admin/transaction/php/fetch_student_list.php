<?php
session_start();
require_once __DIR__ . '/../../../database/connection.php';

$query = "
    SELECT
        u.id,
        u.school_id,
        u.first_name,
        u.last_name,
        u.date_registration,
        sb.pincode,
        sb.biometrics,
        sb.faceset_token,
        CASE
            WHEN sb.student_id IS NULL
                 OR (
                      (sb.pincode IS NULL OR sb.pincode = '')
                  AND (sb.biometrics IS NULL OR sb.biometrics = '')
                  AND (sb.faceset_token IS NULL OR sb.faceset_token = '')
                 )
            THEN 'No biometrics'
            ELSE 'Has biometrics'
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

header('Content-Type: application/json');
echo json_encode($requests);

$conn->close();
