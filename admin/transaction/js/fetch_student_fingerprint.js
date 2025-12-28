document.addEventListener("DOMContentLoaded", function () {
  const tableBody = document.querySelector("#studentsTable tbody");

  fetch("../admin/transaction/php/fetch_student_fingerprint.php")
    .then((response) => response.json())
    .then((data) => {
      if (!data.length) {
        tableBody.innerHTML = `
          <tr>
            <td colspan="4" class="text-muted py-4">
              No students found.
            </td>
          </tr>
        `;
        return;
      }

      tableBody.innerHTML = data
        .map((row, index) => {
          let statusBadge = "";
          const status = (row.status ?? "").toLowerCase();

          switch (status) {
            case "has fingerprint":
              statusBadge =
                '<span class="badge bg-success">Fingerprint Registered</span>';
              break;

            case "no fingerprint registered":
              statusBadge = `
                <span class="badge bg-warning text-dark">No fingerprint registered</span>
                <button class="btn btn-sm btn-primary ms-2 enroll-btn" data-id="${row.school_id}">
                  Enroll / Update
                </button>
              `;
              break;

            case "not yet requested to biometrics":
              statusBadge =
                '<span class="badge bg-info text-dark">Not yet requested</span>';
              break;

            case "inactive":
              statusBadge = '<span class="badge bg-secondary">Inactive</span>';
              break;

            default:
              statusBadge = '<span class="badge bg-secondary">Unknown</span>';
          }

          return `
            <tr>
              <td>${index + 1}</td>
              <td>${row.school_id}</td>
              <td>${row.first_name} ${row.last_name}</td>
              <td>${statusBadge}</td>
            </tr>
          `;
        })
        .join("");

      // Attach click handler for enroll/update buttons
      document.querySelectorAll(".enroll-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
          const studentId = this.getAttribute("data-id");
          // Redirect or open modal for enrollment/update
          window.location.href = `../admin/enroll_biometrics.php?student_id=${studentId}`;
        });
      });
    })
    .catch((error) => {
      console.error("Error fetching student list:", error);
      tableBody.innerHTML = `
        <tr>
          <td colspan="4" class="text-danger py-4">
            Error loading student list. Please try again later.
          </td>
        </tr>
      `;
    });
});
