document.addEventListener("DOMContentLoaded", () => {
  const paymentForms = document.querySelectorAll("form[id^='paymentForm']");

  paymentForms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(form);
      const modalEl = form.closest(".modal");

      // Accessibility: mark modal as visible
      modalEl.setAttribute("aria-hidden", "false");
      document.body.inert = true;

      fetch("../member/transaction/php/payment_transaction.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "success") {
            alert(data.message);
            form.reset();

            setTimeout(() => {
              const modal =
                bootstrap.Modal.getInstance(modalEl) ||
                new bootstrap.Modal(modalEl);
              modal.hide();

              // Restore accessibility state
              modalEl.setAttribute("aria-hidden", "true");
              document.body.inert = false;

              // Reload page after modal closes
              window.location.reload();
            }, 500);
          } else {
            alert("Error: " + data.message);
            modalEl.setAttribute("aria-hidden", "false");
            document.body.inert = true;
          }
        })
        .catch((err) => {
          console.error(err);
          alert("An unexpected error occurred.");
          modalEl.setAttribute("aria-hidden", "false");
          document.body.inert = true;
        });
    });
  });
});
