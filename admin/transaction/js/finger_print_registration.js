document.addEventListener("DOMContentLoaded", function () {
  const registerBtn = document.getElementById("registerFingerprintBtn");
  const notifDiv = document.getElementById("fingerprintNotif");

  registerBtn.addEventListener("click", function () {
    notifDiv.innerHTML =
      "<div class='alert alert-info'>Triggering fingerprint registration. Please place your finger...</div>";

    const esp32IP = "192.168.1.103"; // your ESP32 IP
    const studentId = "<?= htmlspecialchars($student_id) ?>"; // PHP student ID

    // Step 1: Register fingerprint on ESP32
    fetch(`http://${esp32IP}/register`)
      .then((res) => {
        if (!res.ok) throw new Error("ESP32 not reachable");
        return res.json();
      })
      .then((data) => {
        if (data.status === "registered") {
          // Step 2: Send the returned ID to PHP backend
          return fetch("../admin/transaction/php/save_fingerprint.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `student_id=${encodeURIComponent(
              studentId
            )}&fingerprintId=${encodeURIComponent(data.id)}`,
          })
            .then((res) => res.json())
            .then((dbResp) => {
              if (dbResp.status === "success") {
                notifDiv.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  Fingerprint successfully registered and saved! ID: ${data.id}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
                registerBtn.disabled = true;
              } else {
                notifDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  Failed to save fingerprint: ${dbResp.message}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
              }
            });
        } else {
          throw new Error(data.message || "Fingerprint registration failed");
        }
      })
      .catch((err) => {
        console.error("Error:", err);
        notifDiv.innerHTML = `
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Could not complete registration: ${err.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>`;
      });
  });
});
