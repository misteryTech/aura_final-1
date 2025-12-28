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

      <!-- Notification placeholder -->
      <div id="fingerprintNotif"></div>

      <!-- Wrap the form in a container -->
      <div id="fingerprintFormContainer">
        <form id="finger_print_registration">
          <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">

          <div class="mb-3">
            <label for="fingerprintId" class="form-label">Fingerprint ID</label>
            <input type="number" class="form-control" id="fingerprintId" name="fingerprintId" required>
          </div>

          <button type="submit" class="btn btn-primary">Save Fingerprint</button>
        </form>
      </div>
    </div>
  </div>
</div>


  </div>
</section>

<script src="transaction/js/finger_print_registration.js"></script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>