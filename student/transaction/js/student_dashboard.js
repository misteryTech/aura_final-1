document.addEventListener("DOMContentLoaded", function () {
  const biometricModal = new bootstrap.Modal(
    document.getElementById("biometricModal"),
    {
      backdrop: "static",
      keyboard: false,
    }
  );
  biometricModal.show();
});
