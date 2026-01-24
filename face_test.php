<?php
require_once __DIR__ . '/database/connection.php';

$resultMessage = '';

/* =======================
   Face++ Config
======================= */
define('FACEPP_API_KEY', 'gCB6xe-0lpTtGHWVsSBReG3f3paQvUf8');
define('FACEPP_API_SECRET', 'FaT-sI6uN8aLRSldToQIZX9st9LKFy_S');

/* =======================
   Face++ Helper
======================= */
function faceppRequest($url, $fields) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response ? json_decode($response, true) : null;
}

function detectFace($imagePath) {
    return faceppRequest(
        'https://api-us.faceplusplus.com/facepp/v3/detect',
        [
            'api_key'    => FACEPP_API_KEY,
            'api_secret' => FACEPP_API_SECRET,
            'image_file' => new CURLFile($imagePath)
        ]
    );
}

function searchFaceInFaceSet($faceToken, $facesetToken) {
    return faceppRequest(
        'https://api-us.faceplusplus.com/facepp/v3/search',
        [
            'api_key'       => FACEPP_API_KEY,
            'api_secret'    => FACEPP_API_SECRET,
            'face_token'    => $faceToken,
            'faceset_token' => $facesetToken
        ]
    );
}

/* =======================
   Handle POST
======================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id  ='sample1';
    $facial_data = $_POST['facial_data'] ?? '';

    if (!$student_id || !$facial_data) {
        $resultMessage = "<p style='color:red;'>Missing data.</p>";
        goto output;
    }

    /* Fetch FaceSet token */
    $stmt = $conn->prepare("
        SELECT faceset_token
        FROM student_biometrics
        WHERE student_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->bind_result($facesetToken);
    $stmt->fetch();
    $stmt->close();

    if (!$facesetToken) {
        $resultMessage = "<p style='color:red;'>No FaceSet registered for this student.</p>";
        goto output;
    }

    /* Save captured image */
    $imgData = explode(',', $facial_data);
    if (!isset($imgData[1])) {
        $resultMessage = "<p style='color:red;'>Invalid image data.</p>";
        goto output;
    }

    $imageBinary = base64_decode($imgData[1]);
    $tmpImage = __DIR__ . '/temp_face.jpg';
    file_put_contents($tmpImage, $imageBinary);

    /* Detect face */
    $detect = detectFace($tmpImage);
    unlink($tmpImage);

    if (!$detect || !empty($detect['error_message'])) {
        $msg = $detect['error_message'] ?? 'Face detection failed.';
        $resultMessage = "<p style='color:red;'>Face++ Error: {$msg}</p>";
        goto output;
    }

    if (empty($detect['faces'][0]['face_token'])) {
        $resultMessage = "<p style='color:red;'>No face detected.</p>";
        goto output;
    }

    $faceToken = $detect['faces'][0]['face_token'];

    /* Search inside FaceSet */
    $search = searchFaceInFaceSet($faceToken, $facesetToken);

    if (!$search || empty($search['results'][0])) {
        $resultMessage = "<p style='color:red;'>Face not found in FaceSet.</p>";
        goto output;
    }

    $confidence = $search['results'][0]['confidence'];

    $resultMessage  = "<h3>Confidence: {$confidence}</h3>";
    $resultMessage .= ($confidence >= 80)
        ? "<p style='color:green;'>Face Verified ✅</p>"
        : "<p style='color:red;'>Face Not Recognized ❌</p>";
}

output:
?>
<!DOCTYPE html>
<html>
<head>
    <title>Face Verification Test</title>
</head>
<body>

<h2>Face Verification Test</h2>

<?= $resultMessage ?>

<video id="video" width="320" height="240" autoplay></video><br><br>
<button id="capture">Capture & Verify</button>

<canvas id="canvas" width="320" height="240" style="display:none;"></canvas>

<form id="faceForm" method="POST">
    <input type="hidden" name="student_id" value="12345">
    <input type="hidden" name="facial_data" id="facial_data">
</form>

<script>
const video  = document.getElementById('video');
const canvas = document.getElementById('canvas');

navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => video.srcObject = stream)
    .catch(err => alert('Camera access denied'));

document.getElementById('capture').onclick = () => {
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    document.getElementById('facial_data').value =
        canvas.toDataURL('image/jpeg');
    document.getElementById('faceForm').submit();
};
</script>

</body>
</html>
