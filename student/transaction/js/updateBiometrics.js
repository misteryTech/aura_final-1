document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("updateBiometrics");
  const responseBox = document.getElementById("responseBox");

  if (!form) return;

  form.addEventListener("submit", async (event) => {
    event.preventDefault();

    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    const formData = new FormData(form);

    try {
      const response = await fetch(
        "../student/transaction/php/update_biometrics.php",
        {
          method: "POST",
          body: formData,
        }
      );

      const result = await response.json();

      if (result.success) {
        responseBox.textContent =
          result.message || "Biometrics saved successfully!";
        responseBox.className = "mt-3 text-success";
        form.reset();
        form.classList.remove("was-validated");
      } else if (result.reason === "no_data") {
        // 🚨 Show modal if backend says no biometrics data
        const modal = new bootstrap.Modal(
          document.getElementById("requestBiometricsModal")
        );
        modal.show();
      } else {
        responseBox.textContent =
          result.message || "Failed to save biometrics.";
        responseBox.className = "mt-3 text-danger";
      }
    } catch (error) {
      console.error("Fetch error:", error);
      responseBox.textContent = "Error connecting to server.";
      responseBox.className = "mt-3 text-danger";
    }
  });
});
