document.addEventListener("DOMContentLoaded", function () {
  const tableBody = document.querySelector("#studentsTable tbody");

  // Fetch student list from PHP
  fetch("../admin/transaction/php/fetch_student_list.php")
    .then((response) => response.json())
    .then((data) => {
      if (!data.length) {
        tableBody.innerHTML = `
          <tr>
            <td colspan="6" class="text-muted py-4">
              No students found.
            </td>
          </tr>
        `;
        return;
      }

      // Build table rows dynamically
      tableBody.innerHTML = data
        .map((row, index) => {
          let statusBadge = "";

          // Check biometric fields
          const hasPincode = row.pincode && row.pincode.trim() !== "";
          const hasBiometrics = row.biometrics && row.biometrics.trim() !== "";
          const hasFaceToken = row.face_token && row.face_token.trim() !== "";

          if (hasPincode || hasBiometrics || hasFaceToken) {
            const missingFields = [];
            if (!hasPincode) missingFields.push("pincode");
            if (!hasBiometrics) missingFields.push("biometrics");
            if (!hasFaceToken) missingFields.push("face token");

            statusBadge = missingFields.length
              ? `<span class="badge bg-success">Biometrics Registered<br><small>Missing: ${missingFields.join(
                  ", ",
                )}</small></span>`
              : '<span class="badge bg-success">Biometrics Registered</span>';
          } else if ((row.status ?? "").toLowerCase() === "inactive") {
            statusBadge = '<span class="badge bg-secondary">Inactive</span>';
          } else {
            statusBadge =
              '<span class="badge bg-warning text-dark">No biometrics</span>';
          }

          return `
            <tr>
              <td><input type="checkbox" class="selectStudent"></td>
              <td>${index + 1}</td>
              <td>${row.school_id}</td>
              <td>${row.first_name} ${row.last_name}</td>
              <td>${statusBadge}</td>
              <td><svg class="barcode" jsbarcode-format="CODE128"></svg></td>
            </tr>
          `;
        })
        .join("");

      // Generate barcodes in table
      document.querySelectorAll("svg.barcode").forEach((svg) => {
        const schoolID = svg.closest("tr").children[2].textContent.trim();
        JsBarcode(svg, schoolID, {
          format: "CODE128",
          displayValue: true,
          fontSize: 14,
        });
      });

      // Select all checkbox
      const selectAllCheckbox = document.getElementById("selectAll");
      if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", () => {
          document.querySelectorAll(".selectStudent").forEach((cb) => {
            cb.checked = selectAllCheckbox.checked;
          });
        });
      }

      // Print selected barcodes
      const printBtn = document.getElementById("printSelected");
      if (printBtn) {
        printBtn.addEventListener("click", () => {
          const selectedRows = Array.from(
            document.querySelectorAll(".selectStudent:checked"),
          ).map((cb) => cb.closest("tr"));

          if (selectedRows.length === 0)
            return alert("Please select at least one student.");

          const printWindow = window.open("", "_blank");
          printWindow.document.write(`
            <html>
              <head>
                <title>Print Barcodes</title>
                <style>
                  body { font-family: Arial, sans-serif; padding: 20px; }
                  .barcode-grid { display: flex; flex-wrap: wrap; gap: 20px; }
                  .barcode-item {
                    width: 200px; text-align: center; margin-bottom: 20px;
                    page-break-inside: avoid;
                  }
                  svg { width: 100%; height: 60px; }
                  @media print {
                    body { padding: 0; }
                    .barcode-item { margin-bottom: 15px; page-break-inside: avoid; }
                  }
                </style>
              </head>
              <body>
                <div class="barcode-grid">
          `);

          selectedRows.forEach((row) => {
            const schoolID = row.children[2].textContent.trim();
            printWindow.document.write(`
              <div class="barcode-item">
                <svg id="barcode-${schoolID}"></svg>
                <div>${schoolID}</div>
              </div>
            `);
          });

          printWindow.document.write(`
                </div>
                <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"><\/script>
                <script>
                  document.querySelectorAll("svg").forEach(svg => {
                    const id = svg.id.split("-")[1];
                    JsBarcode(svg, id, { format:"CODE128", displayValue:true, fontSize:14 });
                  });
                  window.print();
                <\/script>
              </body>
            </html>
          `);

          printWindow.document.close();
          printWindow.focus();
        });
      }
    })
    .catch((error) => {
      console.error("Error fetching student list:", error);
      tableBody.innerHTML = `
        <tr>
          <td colspan="6" class="text-danger py-4">
            Error loading student list. Please try again later.
          </td>
        </tr>
      `;
    });
});
