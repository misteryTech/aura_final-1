<?php
session_start();


include('templates/header.php');
?>

<main>
<div class="container">
<section class="section register min-vh-100 d-flex align-items-center justify-content-center py-4">
<div class="col-lg-4 col-md-6">

<div class="card">
<div class="card-body">

<h5 class="card-title text-center fs-4">AURA</h5>
<p class="text-center small">ID → Face → Fingerprint → PIN</p>

<div id="responseBox"></div>

<form id="loginProcess">

<!-- STEP 1 -->
<div id="step1">
    <label>Scan School ID QR Code</label>
    <div id="qr-reader" style="width:100%;"></div>
    <div class="text-center mt-2">
        <small>Or enter manually:</small>
        <input type="text" id="school_id" class="form-control mb-2" placeholder="Enter School ID">
    </div>
</div>

<!-- STEP 2 -->
<div id="step2" style="display:none;">
<p class="text-center">Face Recognition</p>
<video id="cameraFeed" autoplay muted playsinline class="w-100 mb-2"></video>
<canvas id="captureCanvas" style="display:none;"></canvas>
<div id="countdown" class="text-center text-danger fs-1"></div>
<button type="button" class="btn btn-secondary w-100" id="scanFaceBtn">Scan Face</button>
</div>


<!-- STEP 3 -->
<div id="stepFingerprint" style="display:none;">
<p class="text-center">Fingerprint Verification</p>
<div id="fingerprintStatus" class="alert alert-info text-center">
Place your finger on the scanner
</div>
<button type="button" class="btn btn-warning w-100" id="scanFingerprintBtn">Scan Fingerprint</button>
</div>

<!-- STEP 4 -->
<div id="stepPin" style="display:none;">
<label>PIN Code</label>
<input type="password" id="pincode" class="form-control mb-2">
<button type="button" class="btn btn-success w-100" id="verifyPinBtn">Login</button>
</div>

</form>

</div>
</div>

</div>
</section>
</div>
</main>

<?php include('templates/footer.php'); ?>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
const step1 = document.getElementById('step1');
const step2 = document.getElementById('step2');

const stepFingerprint = document.getElementById('stepFingerprint');
const stepPin = document.getElementById('stepPin');

const schoolIdInput = document.getElementById('school_id');
const pincodeInput = document.getElementById('pincode');
const responseBox = document.getElementById('responseBox');

const video = document.getElementById('cameraFeed');
const canvas = document.getElementById('captureCanvas');
const countdown = document.getElementById('countdown');
const ESP32_IP = "192.168.1.103";

/* STEP 1 - QR CODE SCANNER */
function proceedToStep2(school_id) {
    fetch('ajax/check_biometrics.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({school_id})
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            responseBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            return;
        }
        step1.style.display = 'none';
        step2.style.display = 'block';
    })
    .catch(err => {
        responseBox.innerHTML = `<div class="alert alert-danger">Error: ${err}</div>`;
    });
}

// Initialize QR code scanner
let qrScanner = new Html5Qrcode("qr-reader"); // Make sure your step1 div has <div id="qr-reader"></div>
qrScanner.start(
    { facingMode: "environment" }, // use back camera
    { fps: 10, qrbox: 250 },
    (decodedText, decodedResult) => {
        // QR scanned successfully
        schoolIdInput.value = decodedText;
        qrScanner.stop(); // stop camera after successful scan
        proceedToStep2(decodedText);
    },
    (errorMessage) => {
        // optional: scan errors
        // console.log(errorMessage);
    }
);

// Optional: allow manual entry with Enter key
schoolIdInput.addEventListener('keypress', function(e){
    if(e.key === 'Enter') proceedToStep2(schoolIdInput.value.trim());
});

/* STEP 2 FACE RECOGNITION */
navigator.mediaDevices.getUserMedia({video:true})
.then(stream => video.srcObject = stream);

document.getElementById('scanFaceBtn').onclick = () => {
    let t = 3;
    countdown.innerText = t;

    const timer = setInterval(() => {
        t--;
        countdown.innerText = t;
        if (t === 0) {
            clearInterval(timer);
            countdown.innerText = '';

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video,0,0);

            fetch('ajax/verify_face.php',{
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({
                    school_id: schoolIdInput.value,
                    facial_data: canvas.toDataURL('image/jpeg')
                })
            })
            .then(res=>res.json())
            .then(d=>{
                if(!d.success) throw d.message;
                step2.style.display='none';
                stepFingerprint.style.display='block';
            })
            .catch(err=>{
                responseBox.innerHTML=`<div class="alert alert-danger">${err}</div>`;
            });
        }
    },1000);
};

/* STEP 3 FINGERPRINT */
document.getElementById('scanFingerprintBtn').onclick = () => {
    fingerprintStatus.innerText = "Scanning fingerprint...";

    fetch(`http://${ESP32_IP}/verify`)
    .then(res => res.json())
    .then(data => {
        if (data.status !== 'matched') throw data.message;

        return fetch('ajax/verify_fingerprint.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({
                school_id: schoolIdInput.value,
                fingerprint_id: data.id
            })
        });
    })
    .then(res=>res.json())
    .then(d=>{
        if(!d.success) throw d.message;
        stepFingerprint.style.display='none';
        stepPin.style.display='block';
    })
    .catch(err=>{
        fingerprintStatus.className="alert alert-danger text-center";
        fingerprintStatus.innerText=err;
    });
};

/* STEP 4 PIN */
document.getElementById('verifyPinBtn').onclick = async () => {
    const res = await fetch('ajax/verify_pin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            school_id: schoolIdInput.value,
            pincode: pincodeInput.value
        })
    });

    const data = await res.json();

    alert(data.message);
    pincodeInput.value = '';
};
</script>
