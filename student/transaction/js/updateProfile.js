document
  .getElementById("updateProfile")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    // Log all key/value pairs
    // console.log("Form data to be sent:");
    // for (let [key, value] of formData.entries()) {
    //   console.log(key, ":", value);
    // }
fetch("transaction/php/update_profile.php", {
  method: "POST",
  body: formData,
})
  .then((response) => response.text()) // read as text first
  .then((text) => {
    console.log("Raw response:", text); // debug HTML or JSON
    try {
      return JSON.parse(text);
    } catch (e) {
      throw new Error("Invalid JSON: " + text);
    }
  })
  .then((data) => {
    let responseBox = document.getElementById("responseBox");
    if (data.status === "success") {
      responseBox.classList.remove("text-danger");
      responseBox.classList.add("text-success");
      responseBox.textContent = data.message;
    } else {
      responseBox.classList.remove("text-success");
      responseBox.classList.add("text-danger");
      responseBox.textContent = data.message;
    }
  })
  .catch((err) => console.error("Error:", err));

  });


document
  .getElementById("updatePassword")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("transaction/php/update_password.php", {
    method: "POST",
    body: formData,
    })
    .then((response) => response.text()) // read as text first
    .then((text) => {
        console.log("Raw response:", text); // debug HTML or JSON
        try {
        return JSON.parse(text);
        } catch (e) {
        throw new Error("Invalid JSON: " + text);
        }
    })
    .then((data) => {
        let responseBox = document.getElementById("responseBoxes");
        if (data.status === "success") {
        responseBox.classList.remove("text-danger");
        responseBox.classList.add("text-success");
        responseBox.textContent = data.message;
        } else {
        responseBox.classList.remove("text-success");
        responseBox.classList.add("text-danger");
        responseBox.textContent = data.message;
        }
    })
    .catch((err) => console.error("Error:", err));


  });
