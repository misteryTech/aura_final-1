document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(form);

    fetch("assets/js/transaction/submit_deceased_documents.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status) {
          alert(data.message);
          form.reset();
        } else {
          alert(data.message);
        }
      })
      .catch((err) => {
        console.error(err);
        alert("Something went wrong. Please try again.");
      });
  });
});
