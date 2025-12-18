document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("membership_form");
  const responseBox = document.getElementById("responseBox");

  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (!form.checkValidity()) {
        form.classList.add("was-validated");
        return;
      }

      const formData = new FormData(form);

      try {
        const res = await fetch(
          "../member/transaction/php/membership_submit.php",
          {
            method: "POST",
            body: formData,
          }
        );

        const data = await res.json();

        if (data.status === "success") {
          responseBox.innerHTML = `🎉 ${data.message}`;
          form.reset();
          form.classList.remove("was-validated");
          loadMemberCard(); // refresh count after success
        } else {
          responseBox.innerHTML = `❌ ${data.message}`;
        }
      } catch (err) {
        responseBox.innerHTML = `⚠️ Network error: ${err.message}`;
      }
    });
  }

  loadMemberCard();
  setInterval(loadMemberCard, 10000);
});

function loadMemberCard() {
  fetch("../member/transaction/php/membership_count.php")
    .then((response) => response.json())
    .then((data) => {
      const totalRegularMembers = data.total ?? 0;

      document.getElementById("membershipCardContainer").innerHTML = `
        <div class="card shadow-sm border-0">
          <div class="card-body text-center">
            <h5 class="card-title fw-bold">Membership Benefits</h5>
            <p class="mb-3">
              Regular Members are entitled to the following benefits:
            </p>

            <div class="alert alert-success fw-bold">
              Total Regular Members: ${totalRegularMembers}
            </div>

            <ul class="list-group text-start">
              <li class="list-group-item">✔ Full voting rights</li>
              <li class="list-group-item">✔ Participation in special programs</li>
              <li class="list-group-item">✔ Priority access to services</li>
              <li class="list-group-item fw-bold text-primary">
                ✔ Paid Monthly Mortuary Fees to avail ₱40,000 package
              </li>
              <li class="list-group-item">✔ Other exclusive member privileges</li>
            </ul>
          </div>
        </div>
      `;
    })
    .catch((error) => {
      console.error("Error fetching member count:", error);
    });
}
