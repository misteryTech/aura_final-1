<?php
session_start();
ob_start(); // Capture page content
require_once __DIR__ . '/../database/connection.php';
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Student Biometric Requests</h5>

                    <table class="table table-striped table-bordered" id="requestsTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Student ID</th>
                                <th>Status</th>
                                <th>Date Applied</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table will be filled dynamically via JS -->
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</section>
<script src="transaction/js/fetch_request_student.js"></script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
