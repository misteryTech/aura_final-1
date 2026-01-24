<?php
// scan_barcode.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barcode Scanner</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        #reader {
            width: 400px;
            margin: 50px auto;
        }
        #result {
            text-align: center;
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Scan Barcode using Camera</h2>
    <div id="reader"></div>
    <div id="result">No barcode scanned yet</div>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Show the scanned barcode
            document.getElementById('result').innerText = `Scanned Barcode: ${decodedText}`;
        }

        function onScanError(errorMessage) {
            // Handle scan errors if needed
            // console.log(`Scan error: ${errorMessage}`);
        }

        // Initialize the scanner
        let scanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: 250 },
            false
        );
        scanner.render(onScanSuccess, onScanError);
    </script>
</body>
</html>
