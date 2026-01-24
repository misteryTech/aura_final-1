<?php
session_start();
ob_start(); // Capture page content
require_once __DIR__ . '/../database/connection.php';
?>

<section class="section">
  <div class="row">

    <!-- Student List -->
    <div class="col-12">
      <div class="card recent-sales overflow-auto">

        <div class="card-body">
          <h5 class="card-title">Student List <span>| Today</span></h5>

          <!-- Print Button -->
          <button id="printSelected" class="btn btn-primary mb-3">Print Selected</button>

          <table class="table table-border" id="studentsTable">
            <thead>
              <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th scope="col">#</th>
                <th scope="col">School ID</th>
                <th scope="col">Student Name</th>
                <th scope="col">Status</th>
                <th scope="col">Barcode</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="6" class="text-muted py-4">
                  Loading student list...
                </td>
              </tr>
            </tbody>
          </table>

        </div>

      </div>
    </div><!-- End Student List -->

  </div>
</section>

<!-- JsBarcode CDN -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="transaction/js/fetch_student_list.js"></script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
