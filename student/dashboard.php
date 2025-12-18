<?php
session_start();
require_once __DIR__ . '/../database/connection.php';

$showBiometricModal = false;

// Check if student has a record in student_biometrics
if (!empty($_SESSION['school_id'])) {

    $student_id = trim($_SESSION['school_id']);

    $stmt = $conn->prepare("
        SELECT 1
        FROM student_biometrics
        WHERE student_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->store_result();

    // Modal should show ONLY IF student + no biometrics
    if ($_SESSION['position'] === 'student' && $stmt->num_rows === 0) {
        $showBiometricModal = true;
    }

    $stmt->close();
}

ob_start();
?>

<section class="section">
    <div class="row">

        <!-- AURA Welcome Announcement -->
        <?php if (!empty($_SESSION['account']) && $_SESSION['account'] !== "Registered") : ?>
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <h3 class="card-title fw-bold">Welcome to AURA Attendance System</h3>

                        <p class="mb-3">
                            Alabel National High School is excited to introduce the
                            <strong>AURA Automated Attendance Monitoring System</strong>.
                        </p>

                        <div class="alert alert-success">
                            Be part of a smarter, faster, and more reliable way of recording attendance.
                        </div>

                        <a href="student_registration.php" class="btn btn-success btn-lg mt-2">
                            Get Started
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php if ($showBiometricModal): ?>
<!-- Biometric Registration Modal -->
<?php include("dashboard_modal.php") ?>

<script src="transaction/js/student_dashboard.js"></script>

<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
