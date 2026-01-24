<?php
session_start();
ob_start();
require_once __DIR__ . '/../database/connection.php';

$student_id = $_GET['student_id'] ?? null;

$query = "
    SELECT
        u.id,
        u.school_id,
        u.first_name,
        u.last_name,
        u.email,
        u.date_registration,
        sb.biometrics
    FROM user_table u
    INNER JOIN student_biometrics sb
        ON u.school_id = sb.student_id
    WHERE u.school_id = ?
    LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<section class="section">
  <div class="row">

    <!-- Student Credentials -->
    <div class="col-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Student Credentials</h5>
          <?php if ($student): ?>
            <ul>
              <li><strong>School ID:</strong> <?= htmlspecialchars($student['school_id']) ?></li>
              <li><strong>Name:</strong> <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></li>
              <li><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></li>
              <li><strong>Date Registered:</strong> <?= htmlspecialchars($student['date_registration']) ?></li>
              <li><strong>Fingerprint Status:</strong>
                <?= $student['biometrics'] && trim($student['biometrics']) !== ''
                    ? '<span class="badge bg-success">Registered</span>'
                    : '<span class="badge bg-warning text-dark">No fingerprint</span>' ?>
              </li>
            </ul>
          <?php else: ?>
            <p class="text-muted">No student record found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Fingerprint Registration Form -->
    <div class="col-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Register Fingerprint</h5>

          <!-- Fingerprint Button & Notification -->
      <div id="fingerprintFormContainer">
        <button id="registerFingerprintBtn" class="btn btn-primary">Register Fingerprint</button>
        <div id="fingerprintNotif" class="mt-3"></div>
      </div>


        </div>
      </div>
    </div>

  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>

<!-- JS to trigger ESP32 fingerprint registration -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const registerBtn = document.getElementById("registerFingerprintBtn");
  const notifDiv = document.getElementById("fingerprintNotif");

  registerBtn.addEventListener("click", function () {
    notifDiv.innerHTML = "<div class='alert alert-info'>Triggering fingerprint registration. Please place your finger...</div>";

    const esp32IP = "192.168.137.42"; // your ESP32 IP
    const studentId = "<?= htmlspecialchars($student_id) ?>"; // PHP student ID

    // Step 1: Register fingerprint on ESP32
    fetch(`http://${esp32IP}/register`)
      .then(res => {
        if (!res.ok) throw new Error("ESP32 not reachable");
        return res.json();
      })
      .then(data => {
        if (data.status === "registered") {
          // Step 2: Send the returned ID to PHP backend
          return fetch("../admin/transaction/php/save_fingerprint.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `student_id=${encodeURIComponent(studentId)}&fingerprintId=${encodeURIComponent(data.id)}`
          })
          .then(res => res.json())
          .then(dbResp => {
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
      .catch(err => {
        console.error("Error:", err);
        notifDiv.innerHTML = `
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Could not complete registration: ${err.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>`;
      });
  });
});
</script>
