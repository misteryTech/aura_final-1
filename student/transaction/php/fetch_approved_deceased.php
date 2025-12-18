<?php
include('../../../database/connection.php');

$query = "
    SELECT
        dba.id,
        dba.osca_id,
        dba.created_at,
        CONCAT(ut.first_name, ' ', ut.last_name) AS fullname
    FROM deceased_benefit_applications dba
    LEFT JOIN user_table ut
        ON dba.osca_id = ut.osca_id
    WHERE dba.status = 'approved'
    ORDER BY dba.created_at DESC
";

$result = $conn->query($query);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
