document.addEventListener("DOMContentLoaded", () => {
    fetchApprovedDeceased();
});

function fetchApprovedDeceased() {
    fetch("../member/transaction/php/fetch_approved_deceased.php")
      .then((response) => response.json())
      .then((data) => {
        const tbody = document.querySelector("#approvedTable tbody");
        tbody.innerHTML = "";

        if (data.length === 0) {
          tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No approved records found
                        </td>
                    </tr>
                `;
          return;
        }



        data.forEach((row, index) => {
          // Convert created_at into a JS Date object
          const createdAt = new Date(row.created_at);

          // Format: Month name + day number (e.g., December 15)
          const options = { month: "long", day: "numeric" };
          const formattedDate = createdAt.toLocaleDateString("en-US", options);

          tbody.innerHTML += `
        <tr>
            <td>${index + 1}</td>
            <td>${row.fullname ?? "Unknown"}</td>
            <td>${row.osca_id}</td>
            <td>${formattedDate}</td>
        </tr>
    `;
        });


      })
      .catch((error) => {
        console.error("Fetch error:", error);
      });
}
