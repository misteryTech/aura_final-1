<?php
require_once __DIR__ . '/database/connection.php';

$resultMessage = '';

/* =======================
   Face++ Config
======================= */
define('FACEPP_API_KEY', 'gCB6xe-0lpTtGHWVsSBReG3f3paQvUf8');
define('FACEPP_API_SECRET', 'FaT-sI6uN8aLRSldToQIZX9st9LKFy_S');

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

    if ($ch instanceof CurlHandle) {
        curl_close($ch);
    }

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

    if ($ch instanceof CurlHandle) {
        curl_close($ch);
    }

    return json_decode($response, true);
}

/* =======================
   Handle POST
======================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id  =  'student101';
    $facial_data = $_POST['facial_data'] ?? '';

    if (!$student_id || !$facial_data) {
        $resultMessage = "<p style='color:red;'>Missing data.</p>";
    } else {

        // Fetch stored token
        $stmt = $conn->prepare("
            SELECT face_token FROM student_biometrics
            WHERE student_id = ?
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->bind_result($storedToken);
        $stmt->fetch();
        $stmt->close();

        if (!$storedToken) {
            $resultMessage = "<p style='color:red;'>No stored face token.</p>";
        } else {

            // Save captured image
            $img = base64_decode(substr($facial_data, strpos($facial_data, ',') + 1));
            $tmpImage = __DIR__ . '/temp_test.jpg';
            file_put_contents($tmpImage, $img);

            // Detect new face
            $detect = detectFace($tmpImage);
            unlink($tmpImage);

            if (empty($detect['faces'][0]['face_token'])) {
                $resultMessage = "<p style='color:red;'>No face detected.</p>";
            } else {
                $newToken = $detect['faces'][0]['face_token'];

                // Compare
                $compare = compareFaces($storedToken, $newToken);

                $resultMessage .= "<h3>Confidence: {$compare['confidence']}</h3>";

                if ($compare['confidence'] >= 80) {
                    $resultMessage .= "<p style='color:green;'>Face Verified ✅</p>";
                } else {
                    $resultMessage .= "<p style='color:red;'>Face Not Recognized ❌</p>";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Face Token Test</title>
</head>
<body>

<h2>Face Token Test</h2>

<?= $resultMessage ?>

<video id="video" width="320" height="240" autoplay></video><br><br>
<button id="capture">Capture & Test</button>

<canvas id="canvas" width="320" height="240" style="display:none;"></canvas>

<form id="faceForm" method="POST">
    <input type="hidden" name="student_id" value="12345">
    <input type="hidden" name="facial_data" id="facial_data">
</form>

<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');

navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => video.srcObject = stream);

document.getElementById('capture').onclick = () => {
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    document.getElementById('facial_data').value = canvas.toDataURL('image/jpeg');
    document.getElementById('faceForm').submit();
};
</script>

</body>
</html>
