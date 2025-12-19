<?php
session_start();

// Redirect if user already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['position'] === 'Admin') {
        header("Location: admin/dashboard.php");
        exit;
    } else {
        header("Location: member/dashboard.php");
        exit;
    }
}

include('templates/header.php');
?>
<main>
    <div class="container">
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                        <!-- Logo -->
                        <div class="d-flex justify-content-center py-4">
                            <a href="index.php" class="logo d-flex align-items-center w-auto">
                                <img src="assets/img/logo.png" alt="">
                                <span class="d-none d-lg-block">ATTENDANCE MONITORING SYSTEM</span>
                            </a>
                        </div>

                        <!-- Card -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title text-center pb-0 fs-4">AURA</h5>
                                <p class="text-center small">Step-by-step login: ID → Face Recognition → PIN</p>

                                <form id="loginProcess" class="needs-validation" novalidate enctype="multipart/form-data">
                                    <div id="responseBox" class="mt-3"></div>

                                    <!-- Step 1: School ID -->
                                    <div id="step1">
                                        <label for="school_id" class="form-label">School ID Number</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">#</span>
                                            <input type="text" class="form-control" id="school_id" name="school_id" required>
                                            <div class="invalid-feedback">Please enter your School ID.</div>
                                        </div>
                                        <button type="button" class="btn btn-primary w-100" id="nextStep1">Next</button>
                                    </div>

                                  <!-- Step 2: Face Recognition -->
                                    <div id="step2" style="display:none;">
                                        <p class="text-center mb-2">Face Recognition</p>
                                        <div class="mb-2 position-relative">
                                            <video id="cameraFeed" width="100%" autoplay muted playsinline></video>
                                            <canvas id="captureCanvas" style="display:none;"></canvas>
                                            <div id="countdown" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);
                                                font-size:48px; color:red; font-weight:bold; display:none;"></div>
                                        </div>
                                        <button type="button" class="btn btn-secondary w-100 mb-3" id="scanFaceBtn">Start Scan</button>
                                    </div>

                                    <!-- Step 3: PIN Verification -->
                                    <div id="step3" style="display:none;">
                                        <label for="pincode" class="form-label">PIN Code</label>
                                        <input type="password" class="form-control mb-3" id="pincode" name="pincode" required>
                                        <div class="invalid-feedback">Please enter your PIN!</div>
                                        <button type="button" class="btn btn-success w-100" id="verifyPinBtn">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="credits text-center">
                            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
</main>


<?php include('templates/footer.php'); ?><script>
const step1 = document.getElementById('step1');
const step2 = document.getElementById('step2');
const step3 = document.getElementById('step3');
const responseBox = document.getElementById('responseBox');

const schoolIdInput = document.getElementById('school_id');
const pincodeInput = document.getElementById('pincode');

const video = document.getElementById('cameraFeed');
const canvas = document.getElementById('captureCanvas');

/* =========================
   STEP 1 — Verify Student ID
========================= */
document.getElementById('nextStep1').onclick = async () => {
    const school_id = schoolIdInput.value.trim();

    if (!school_id) return;

    const res = await fetch('ajax/check_biometrics.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ school_id })
    });

    const data = await res.json();

    if (!data.success) {
        responseBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        return;
    }

    step1.style.display = 'none';
    step2.style.display = 'block';
};

/* =========================
   CAMERA AUTO START
========================= */
navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => video.srcObject = stream)
    .catch(() => alert('Camera access denied'));

/* =========================
   STEP 2 — FACE RECOGNITION
========================= *//* =========================
   STEP 2 — FACE RECOGNITION with Countdown
========================= */
const countdownEl = document.getElementById('countdown');

document.getElementById('scanFaceBtn').onclick = () => {
    let timer = 3; // 3-second countdown
    countdownEl.style.display = 'block';
    countdownEl.textContent = timer;

    const interval = setInterval(() => {
        timer--;
        if (timer > 0) {
            countdownEl.textContent = timer;
        } else {
            clearInterval(interval);
            countdownEl.style.display = 'none';

            // Capture the face
            const ctx = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0);

            const facial_data = canvas.toDataURL('image/jpeg');

            // Send for face verification
            fetch('ajax/verify_face.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    school_id: schoolIdInput.value,
                    facial_data
                })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    responseBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    return;
                }

                // Face verified → show PIN step
                step2.style.display = 'none';
                step3.style.display = 'block';
            })
            .catch(err => {
                responseBox.innerHTML = `<div class="alert alert-danger">Face verification failed.</div>`;
                console.error(err);
            });
        }
    }, 1000);
};
/* =========================
   STEP 3 — PIN VERIFICATION
========================= */
document.getElementById('verifyPinBtn').onclick = async () => {
    const pincode = pincodeInput.value.trim();
    if (!pincode) return;

    const res = await fetch('ajax/verify_pin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            school_id: schoolIdInput.value,
            pincode
        })
    });

    const raw = await res.text();
    console.log(raw); // DEBUG

    let data;
    try {
        data = JSON.parse(raw);
    } catch (e) {
        responseBox.innerHTML = `<div class="alert alert-danger">Server Error: Invalid JSON</div>`;
        return;
    }

    if (!data.success) {
        responseBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        return;
    }

    window.location.href = data.redirect;
};
</script>
