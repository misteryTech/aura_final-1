const video = document.getElementById("camera");
const canvas = document.getElementById("snapshot");
const captureBtn = document.getElementById("captureBtn");
const saveBtn = document.getElementById("saveBtn");

const profileImg = document.getElementById("student_image");
const formPreview = document.getElementById("formPreview");
const facialDataInput = document.getElementById("facial_data");
const studentIdInput = document.querySelector("input[name='student_id']");

// Start camera
navigator.mediaDevices
  .getUserMedia({ video: true })
  .then((stream) => {
    video.srcObject = stream;
  })
  .catch((error) => {
    alert("Camera access denied or failed.");
    console.error(error);
  });

// Capture face
captureBtn.addEventListener("click", () => {
  const context = canvas.getContext("2d");
  context.drawImage(video, 0, 0, canvas.width, canvas.height);

  const dataUrl = canvas.toDataURL("image/png");
  facialDataInput.value = dataUrl;

  profileImg.src = dataUrl;
  formPreview.src = dataUrl;

  saveBtn.disabled = false;
});

document
  .getElementById("updateFaceForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    if (!facialDataInput.value) {
      alert("Please capture the face first.");
      return;
    }

    saveBtn.disabled = true;
    saveBtn.innerText = "Saving...";

    fetch("../admin/transaction/php/save_student_biometrics.php", {
      method: "POST",
      body: new FormData(this),
    })
      .then((res) => res.text())
      .then((data) => {

        alert("Saved successfully!");
        saveBtn.disabled = false;
        saveBtn.innerText = "Save Facial Biometrics";
      })
      .catch((err) => {
        console.error(err);
        alert("Error saving biometric.");
        saveBtn.disabled = false;
        saveBtn.innerText = "Save Facial Biometrics";
      });
  });
