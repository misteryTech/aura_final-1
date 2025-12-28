document.addEventListener("DOMContentLoaded", function () {
  const tableBody = document.querySelector("#studentsTable tbody");

  fetch("../admin/transaction/php/fetch_student_list.php")
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

          // Check biometric fields
          const hasPincode = row.pincode && row.pincode.trim() !== "";
          const hasBiometrics = row.biometrics && row.biometrics.trim() !== "";
          const hasFaceToken = row.face_token && row.face_token.trim() !== "";

          if (hasPincode || hasBiometrics || hasFaceToken) {
            // Build list of missing fields
            const missingFields = [];
            if (!hasPincode) missingFields.push("pincode");
            if (!hasBiometrics) missingFields.push("biometrics");
            if (!hasFaceToken) missingFields.push("face token");

            if (missingFields.length > 0) {
              statusBadge = `<span class="badge bg-success">Biometrics Registered<br><small>Missing: ${missingFields.join(
                ", "
              )}</small></span>`;
            } else {
              statusBadge =
                '<span class="badge bg-success">Biometrics Registered</span>';
            }
          } else if ((row.status ?? "").toLowerCase() === "inactive") {
            statusBadge = '<span class="badge bg-secondary">Inactive</span>';
          } else {
            statusBadge =
              '<span class="badge bg-warning text-dark">No biometrics</span>';
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
