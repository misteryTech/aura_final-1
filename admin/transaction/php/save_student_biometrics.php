<?php
require_once __DIR__ . '/../../../database/connection.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* =========================
   Face++ Config
========================= */
define('FACEPP_API_KEY', 'gCB6xe-0lpTtGHWVsSBReG3f3paQvUf8');
define('FACEPP_API_SECRET', 'FaT-sI6uN8aLRSldToQIZX9st9LKFy_S');

define('FACEPP_DETECT_URL', 'https://api-us.faceplusplus.com/facepp/v3/detect');
define('FACEPP_CREATE_FACESET_URL', 'https://api-us.faceplusplus.com/facepp/v3/faceset/create');
define('FACEPP_ADD_FACE_URL', 'https://api-us.faceplusplus.com/facepp/v3/faceset/addface');

/**
 * Human readable ID (Face++ recommended)
 * FaceSet will be created ONCE
 */
define('FACEPP_FACESET_OUTER_ID', 'students_faceset');

/* =========================
   Create or Get FaceSet
========================= */
function getOrCreateFaceSet()
{
    $ch = curl_init(FACEPP_CREATE_FACESET_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'api_key'    => FACEPP_API_KEY,
            'api_secret' => FACEPP_API_SECRET,
            'outer_id'   => FACEPP_FACESET_OUTER_ID,
            'display_name' => 'Student Face Registry'
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!empty($data['faceset_token'])) {
        return $data['faceset_token'];
    }

    // If already exists, fetch it
    if (!empty($data['error_message']) && str_contains($data['error_message'], 'FACESET_EXIST')) {
        return fetchFaceSetToken();
    }

    throw new Exception("FaceSet creation failed: " . $response);
}

/* =========================
   Fetch Existing FaceSet
========================= */
function fetchFaceSetToken()
{
    $ch = curl_init('https://api-us.faceplusplus.com/facepp/v3/faceset/getdetail');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'api_key'    => FACEPP_API_KEY,
            'api_secret' => FACEPP_API_SECRET,
            'outer_id'   => FACEPP_FACESET_OUTER_ID
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (empty($data['faceset_token'])) {
        throw new Exception("Unable to fetch FaceSet");
    }

    return $data['faceset_token'];
}

/* =========================
   Detect Face
========================= */
function detectFace($imagePath)
{
    $ch = curl_init(FACEPP_DETECT_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'api_key' => FACEPP_API_KEY,
            'api_secret' => FACEPP_API_SECRET,
            'image_file' => new CURLFile($imagePath)
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

/* =========================
   Add Face to FaceSet
========================= */
function addFaceToFaceSet($faceToken, $facesetToken)
{
    $ch = curl_init(FACEPP_ADD_FACE_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'api_key' => FACEPP_API_KEY,
            'api_secret' => FACEPP_API_SECRET,
            'faceset_token' => $facesetToken,
            'face_tokens' => $faceToken
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

/* =========================
   MAIN
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id  = $_POST['student_id'] ?? '';
    $facial_data = $_POST['facial_data'] ?? '';

    if (!$student_id || !$facial_data) {
        die("Missing data.");
    }

    $conn->begin_transaction();

    try {
        /* Upload image */
        $uploadDir = __DIR__ . '/../../../uploads/student_biometrics/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $imageData = base64_decode(substr($facial_data, strpos($facial_data, ',') + 1));
        $fileName = "facial_{$student_id}_" . time() . ".jpg";
        $filePath = $uploadDir . $fileName;
        file_put_contents($filePath, $imageData);

        /* Detect face */
        $detect = detectFace($filePath);
        if (empty($detect['faces'][0]['face_token'])) {
            throw new Exception("No face detected");
        }

        $faceToken = $detect['faces'][0]['face_token'];

        /* Get or create FaceSet */
        $facesetToken = getOrCreateFaceSet();

        /* Add face permanently */
        $add = addFaceToFaceSet($faceToken, $facesetToken);
        if (empty($add['face_added'])) {
            throw new Exception("Failed to add face to FaceSet");
        }

        /* Save DB */
        $stmt = $conn->prepare("
            INSERT INTO student_biometrics
            (student_id, facial_url, face_token, faceset_token)
            VALUES (?, ?, ?, ?)
        ");

        $relativePath = "uploads/student_biometrics/$fileName";
        $stmt->bind_param("ssss", $student_id, $relativePath, $faceToken, $facesetToken);
        $stmt->execute();

        $conn->commit();
        header("Location: ../../student_profile.php?student_id=$student_id&saved=1");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        if (isset($filePath) && file_exists($filePath)) unlink($filePath);
        die("ERROR: " . $e->getMessage());
    }
}
