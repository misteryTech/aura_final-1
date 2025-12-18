document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registerForm");
  const responseBox = document.getElementById("responseBox");

  form.addEventListener("submit", async (e) => {
    e.preventDefault(); // stop normal form submission

    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    const formData = new FormData(form);

    try {
      const res = await fetch("assets/js/transaction/registration.php", {
        // change to your endpoint
        method: "POST",
        body: formData,
      });

      if (res.ok) {
        const data = await res.text(); // or res.json() if backend returns JSON
        responseBox.innerHTML =
          "🎉 Successfully submitted! Server says: " + data;
      } else {
        responseBox.innerHTML = "❌ Error submitting form.";
      }
    } catch (err) {
      responseBox.innerHTML = "⚠️ Network error: " + err.message;
    }
  });
});
