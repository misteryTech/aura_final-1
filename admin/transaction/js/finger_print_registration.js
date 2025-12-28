document.addEventListener("DOMContentLoaded", function () {
  const fingerprintForm = document.getElementById("finger_print_registration");
  const notifDiv = document.getElementById("fingerprintNotif");

  fingerprintForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(fingerprintForm);

    fetch("../admin/transaction/php/save_fingerprint.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {

          // Show success message
          notifDiv.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              Already Registered to biometrics
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;

          // Disable the form fields and button
          fingerprintForm.querySelectorAll("input, button").forEach((el) => {
            el.disabled = true;
          });
        } else {
          notifDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              ${data.message}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
        }
      })
      .catch((error) => {
        console.error("Error registering fingerprint:", error);
        notifDiv.innerHTML = `
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            An error occurred while registering the fingerprint.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>`;
      });
  });
});
