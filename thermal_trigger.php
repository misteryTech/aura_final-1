<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Record Thermal Temperature</title>
<style>
button { padding: 10px 20px; font-size: 16px; cursor: pointer; }
#result { margin-top: 20px; font-weight: bold; }
</style>
<script>
async function recordTemp() {
    const espIp = "192.168.1.103"; // ESP32 IP
    const resultDiv = document.getElementById("result");
    resultDiv.innerHTML = "Reading temperature...";

    try {
        // Fetch JSON from ESP32
        const response = await fetch(`http://${espIp}/trigger`, {
            method: "GET",
            mode: "cors"
        });

        if (!response.ok) {
            throw new Error("ESP32 request failed: " + response.status);
        }

        const data = await response.json(); // Expecting JSON: {min, max, avg}

        // Display on page
        resultDiv.innerHTML = `
            Min Temperature: ${data.min} °C<br>
            Max Temperature: ${data.max} °C<br>
            Avg Temperature: ${data.avg} °C
        `;

        // Send all data to PHP backend
        const postData = new URLSearchParams();
        postData.append("min", data.min);
        postData.append("max", data.max);
        postData.append("avg", data.avg);

        const saveResponse = await fetch("save_avg.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: postData.toString()
        });

        const saveText = await saveResponse.text();
        console.log("Saved to DB:", saveText);

    } catch (err) {
        console.error(err);
        resultDiv.innerHTML = "Error reading temperature. Check ESP32 CORS or network!";
    }
}
</script>
</head>
<body>
<h1>Thermal Camera Temperature Recorder</h1>
<button onclick="recordTemp()">Record Thermal Temperature</button>
<div id="result"></div>
</body>
</html>
