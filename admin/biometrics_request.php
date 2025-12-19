<?php
session_start();
ob_start();
require_once __DIR__ . '/../database/connection.php';

if (!isset($_GET['student_id'])) {
    echo "<div class='alert alert-danger'>Invalid Student ID.</div>";
    exit;
}

$student_id = $_GET['student_id'];
?>

<section class="section">
    <div class="row justify-content-center">

        <!-- Left Column: Profile Card + Live Camera -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <!-- Profile Picture as square -->
                    <img id="student_image"
                         src="../member/assets/default_profile.png"
                         class="mb-3"
                         width="140" height="140"
                         style="border-radius: 0; object-fit: cover;">

                    <p class="text-muted">
                        Student ID: <span id="profile_student_id"><?= $student_id ?></span>
                    </p>

                    <h5 class="mt-3">Live Camera</h5>
                    <video id="camera" autoplay playsinline style="width:100%; border-radius:0;"></video>
                    <canvas id="snapshot" width="640" height="480" style="display:none;"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column: Captured Image + Submit Form -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="mb-3">Captured Face</h5>

  <!-- Camera preview -->


                        <!-- Form to submit captured image -->
                        <form id="updateFaceForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="student_id" id="student_id" value="<?= $student_id ?>">
                            <input type="hidden" name="facial_data" id="facial_data">

                            <div class="border rounded p-2 mb-3">
                                <img id="formPreview"
                                    src="../member/assets/default_profile.png"
                                    style="width:200px; height:200px; object-fit:cover;">
                            </div>

                            <button type="button" id="captureBtn" class="btn btn-primary w-100 mb-2">
                                Capture Face
                            </button>

                            <button type="submit" id="saveBtn" class="btn btn-success w-100" disabled>
                                Save Facial Biometrics
                            </button>
                        </form>

                </div>
            </div>
        </div>

    </div>
</section>

<script src="transaction/js/facial_recognition.js"></script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
