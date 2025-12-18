<?php
include('../../../database/connection.php');

$response = ['status' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    function uploadFile($file, $folder = 'uploads/deceased/') {
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        if ($file['error'] === 0) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $path = $folder . $filename;

            if (move_uploaded_file($file['tmp_name'], $path)) {
                return $path;
            }
        }
        return null;
    }

    $osca_id        = $_POST['osca_id'];
    $deceased_name  = $_POST['deceased_name'];
    $dob            = $_POST['dob'];
    $date_of_death  = $_POST['date_of_death'];
    $claimant_name  = $_POST['claimant_name'];
    $relationship   = $_POST['relationship'];
    $contact        = $_POST['contact'];
    $address        = $_POST['address'];

    $death_cert     = uploadFile($_FILES['death_certificate']);
    $osca_file      = uploadFile($_FILES['osca_id_file']);
    $claimant_id    = uploadFile($_FILES['claimant_id']);
    $barangay_clear = uploadFile($_FILES['barangay_clearance']);

    $stmt = $conn->prepare("
        INSERT INTO deceased_benefit_applications
        (osca_id, deceased_name, dob, date_of_death,
         claimant_name, relationship, contact, address,
         death_certificate, osca_id_file, claimant_id, barangay_clearance)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "ssssssssssss",
        $osca_id,
        $deceased_name,
        $dob,
        $date_of_death,
        $claimant_name,
        $relationship,
        $contact,
        $address,
        $death_cert,
        $osca_file,
        $claimant_id,
        $barangay_clear
    );

    if ($stmt->execute()) {
        $response = [
            'status' => true,
            'message' => 'Application submitted successfully. Please wait for approval.'
        ];
    } else {
        $response['message'] = 'Failed to submit application.';
    }
}

echo json_encode($response);
