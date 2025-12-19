document.addEventListener("DOMContentLoaded", function () {
  const tableBody = document.querySelector("#requestsTable tbody");

  fetch("../admin/transaction/php/fetch_student_request.php")
    .then((response) => response.json())
    .then((data) => {
      if (!data.length) {
        tableBody.innerHTML = `
          <tr>
              <td colspan="5" class="text-center text-muted py-4">
                  No student biometric requests found.
              </td>
          </tr>
        `;
        return;
      }

      tableBody.innerHTML = data
        .map((row) => {
          let statusBadge = "";
          switch ((row.status ?? "pending").toLowerCase()) {
            case "pending":
              statusBadge =
                '<span class="badge bg-warning text-dark">Pending</span>';
              break;
            case "approved":
              statusBadge = '<span class="badge bg-success">Approved</span>';
              break;
            case "rejected":
              statusBadge = '<span class="badge bg-danger">Rejected</span>';
              break;
            default:
              statusBadge = '<span class="badge bg-secondary">Unknown</span>';
          }

          return `
            <tr>
                <td>${row.student_id}</td>
                <td>${statusBadge}</td>
                <td>${row.date_request}</td>
                <td>${row.remarks ?? ""}</td>

                <td>
                    <a href="../admin/biometrics_request.php?student_id=${
                      row.student_id
                    }"
                       class="btn btn-primary btn-sm">
                       View Profile
                    </a>
                </td>
            </tr>
          `;
        })
        .join("");
    })
    .catch((error) => {
      console.error("Error fetching student requests:", error);
      tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center text-danger py-4">
                Failed to load student requests.
            </td>
        </tr>
      `;
    });
});
