<?php
session_start();
ob_start(); // Capture page content
require_once __DIR__ . '/../database/connection.php';
?>

<section class="section">
  <div class="row">

    <!-- Recent Sales -->
    <div class="col-12">
      <div class="card recent-sales overflow-auto">

        <div class="card-body">
          <h5 class="card-title">Recent Sales <span>| Today</span></h5>

          <table class="table table-border " id="studentsTable">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">School ID</th>
                <th scope="col">Student Name</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <!-- Rows will be injected by fetch_student_list.js -->
              <tr>
                <td colspan="4" class="text-muted py-4">
                  Loading student list...
                </td>
              </tr>
            </tbody>
          </table>

        </div>

      </div>
    </div><!-- End Recent Sales -->

  </div>
</section>

<script src="transaction/js/fetch_student_list.js"></script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>