<?php
require_once __DIR__ . '/../../../database/connection.php';

/* =========================
   Face++ Configuration
========================= */
define('FACEPP_API_KEY', 'gCB6xe-0lpTtGHWVsSBReG3f3paQvUf8');
define('FACEPP_API_SECRET', 'FaT-sI6uN8aLRSldToQIZX9st9LKFy_S');
define('FACEPP_DETECT_URL', 'https://api-us.faceplusplus.com/facepp/v3/detect');

/* =========================
   Face++ Detect Function
========================= */
function detectFace($imagePath)
{
    $postData = [
        'api_key'    => FACEPP_API_KEY,
        'api_secret' => FACEPP_API_SECRET,
        'image_file' => new CURLFile($imagePath),
        'return_landmark' => '0',
        'return_attributes' => 'none'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => FACEPP_DETECT_URL,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

/* =========================
   Main Logic
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id  = trim($_POST['student_id'] ?? '');
    $facial_data = $_POST['facial_data'] ?? '';

    if (empty($student_id) || empty($facial_data)) {
        die("Missing data.");
    }

    /* =========================
       Upload Directory
    ========================= */
    $uploadDir = __DIR__ . '/../../../uploads/student_biometrics/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    /* =========================
       Decode Base64 Image
    ========================= */
    if (!preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $facial_data, $type)) {
        die("Invalid image format.");
    }

    $imageData = base64_decode(substr($facial_data, strpos($facial_data, ',') + 1));
    if ($imageData === false) {
        die("Image decode failed.");
    }

    $extension = $type[1];
    $fileName  = 'facial_' . $student_id . '_' . time() . '.' . $extension;
    $filePath  = $uploadDir . $fileName;

    if (!file_put_contents($filePath, $imageData)) {
        die("Failed to save image.");
    }

    /* =========================
       Face++ Detect
    ========================= */
    $faceResult = detectFace($filePath);

    if (
        empty($faceResult['faces']) ||
        empty($faceResult['faces'][0]['face_token'])
    ) {
        unlink($filePath); // cleanup
        die("No face detected. Please try again.");
    }

    $faceToken = $faceResult['faces'][0]['face_token'];

    /* =========================
       Save to Database
    ========================= */
    $relativePath = 'uploads/student_biometrics/' . $fileName;

    $stmt = $conn->prepare("
        INSERT INTO student_biometrics (student_id, facial_url, face_token)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("sss", $student_id, $relativePath, $faceToken);

    if ($stmt->execute()) {
        header("Location: ../../student_profile.php?student_id={$student_id}&saved=1");
        exit;
    } else {
        unlink($filePath);
        die("Failed to save biometric data.");
    }
}
?>
