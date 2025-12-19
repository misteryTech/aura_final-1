<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../database/connection.php'; // adjust path if needed

/* =======================
   Face++ Config
======================= */
define('FACEPP_API_KEY', 'gCB6xe-0lpTtGHWVsSBReG3f3paQvUf8');
define('FACEPP_API_SECRET', 'FaT-sI6uN8aLRSldToQIZX9st9LKFy_S');

/* =======================
   Functions
======================= */
function detectFace($imagePath) {
    $ch = curl_init('https://api-us.faceplusplus.com/facepp/v3/detect');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'api_key' => FACEPP_API_KEY,
            'api_secret' => FACEPP_API_SECRET,
            'image_file' => new CURLFile($imagePath)
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    $response = curl_exec($ch);
    if ($ch instanceof CurlHandle) curl_close($ch);
    return json_decode($response, true);
}

function compareFaces($token1, $token2) {
    $ch = curl_init('https://api-us.faceplusplus.com/facepp/v3/compare');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'api_key' => FACEPP_API_KEY,
            'api_secret' => FACEPP_API_SECRET,
            'face_token1' => $token1,
            'face_token2' => $token2
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    $response = curl_exec($ch);
    if ($ch instanceof CurlHandle) curl_close($ch);
    return json_decode($response, true);
}

/* =======================
   Handle POST Request
======================= */
$data = json_decode(file_get_contents('php://input'), true);
$student_id  = $data['school_id'] ?? null;
$facial_data = $data['facial_data'] ?? null;

if (!$student_id || !$facial_data) {
    echo json_encode(['success' => false, 'message' => 'Missing student ID or facial data']);
    exit;
}

// Fetch stored face token
$stmt = $conn->prepare("SELECT face_token FROM student_biometrics WHERE student_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$stmt->bind_result($storedToken);
$stmt->fetch();
$stmt->close();

if (!$storedToken) {
    echo json_encode(['success' => false, 'message' => 'No stored face found for this student']);
    exit;
}

// Save captured image temporarily
$imagePath = __DIR__ . '/temp_face.jpg';
$imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $facial_data));
file_put_contents($imagePath, $imageData);

// Detect face
$detect = detectFace($imagePath);
unlink($imagePath);

if (empty($detect['faces'][0]['face_token'])) {
    echo json_encode(['success' => false, 'message' => 'No face detected in the image']);
    exit;
}

$newToken = $detect['faces'][0]['face_token'];

// Compare faces
$compare = compareFaces($storedToken, $newToken);

$threshold = 80; // minimum confidence for verification

if (!isset($compare['confidence'])) {
    echo json_encode(['success' => false, 'message' => 'Face comparison failed']);
    exit;
}

if ($compare['confidence'] >= $threshold) {
    echo json_encode([
        'success' => true,
        'message' => 'Face verified',
        'confidence' => $compare['confidence']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Face not recognized',
        'confidence' => $compare['confidence']
    ]);
}
