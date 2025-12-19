document.addEventListener("DOMContentLoaded", function () {
  // Show modal
  const modalEl = document.getElementById("biometricModal");
  if (modalEl) {
    const biometricModal = new bootstrap.Modal(modalEl, {
      backdrop: "static",
      keyboard: false,
    });
    biometricModal.show();
  }

  // Form submit
  const form = document.getElementById("requestForm");
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(form);

      fetch("../student/transaction/php/submit_request.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            alert("Biometric request submitted successfully!");

            // Close modal
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            modalInstance.hide();

            // Optional: reload page
            location.reload();
          } else {
            alert(data.message || "Something went wrong!");
          }
        })
        .catch((err) => {
          console.error(err);
          alert("Error submitting request");
        });
    });
  }
});
