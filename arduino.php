<!DOCTYPE html>
<html>
<head>
    <title>ESP32 Fingerprint Scan</title>
    <style>
        body { font-family: Arial; text-align: center; padding-top: 50px; }
        button { padding: 15px 25px; font-size: 18px; cursor: pointer; }
        #result { margin-top: 20px; font-size: 20px; }
    </style>
</head>
<body>
    <h2>Fingerprint Scan</h2>
    <form method="POST">
        <button type="submit" name="scan">Scan Fingerprint</button>
    </form>

    <div id="result">
        <?php
        if (isset($_POST['scan'])) {
            $esp32_ip = "192.168.137.42"; // ESP32 IP
            $scan_id  = "1";               // Fingerprint ID to verify

            // ESP32 scan URL
            $url = "http://$esp32_ip/scan?id=$scan_id";

            // Send HTTP request
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); // wait for fingerprint
            $response = curl_exec($ch);

            if ($response === false) {
                echo "<span style='color:red;'>❌ Cannot connect to ESP32</span>";
            } else {
                $data = json_decode($response, true);
                if ($data) {
                    if ($data["status"] === "matched") {
                        echo "✅ <b>Fingerprint MATCHED</b><br>ID: " . $data["id"];
                    } elseif ($data["status"] === "not_matched") {
                        echo "❌ <b>Fingerprint NOT MATCHED</b>";
                    } else {
                        echo "⚠️ <b>Scan Failed</b>";
                    }
                } else {
                    echo "⚠️ <b>Invalid response from ESP32</b>";
                }
            }

            curl_close($ch);
        }
        ?>
    </div>
</body>
</html>
