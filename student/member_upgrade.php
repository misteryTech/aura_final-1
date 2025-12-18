<?php
session_start();

$osca_id = $_SESSION['osca_id'];
$fullname = $_SESSION['first_name'] . ' ' . $_SESSION ['last_name'];
$chapter = $_SESSION['chapter'];
ob_start(); // Capture page content
?>
<section class="section">
    <div class="row">

        <!-- Regular Membership Application Form -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="card-title fw-bold text-center">Regular Membership Application Form</h3>
                    <p class="text-center mb-4">
                        Please fill out the form below to apply for <strong>Regular Membership</strong>.
                    </p>
                                    <form id="membership_form" class="row g-3 needs-validation" novalidate>
                                          <div id="responseBox" class="mt-3 text-success"></div>
                                        <div class="row mb-3">
                                            <input type="text" value="<?= $osca_id ?>" name="osca_id" hidden>

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Full Name</label>
                                                <h3><?= $fullname ?></h3>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">OSCA ID No.</label>
                                                <h4 class="text-primary"><?= $osca_id ?></h4>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Chapter</label>
                                                <h4><?= $chapter ?></h4>
                                            </div>
                                        </div>

                                        <hr>

                                        <h4 class="fw-bold">Contact Person Information</h4>

                                        <div class="row mt-4 mb-3">

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Full Name</label>
                                                <input type="text" name="cp_fullname" class="form-control" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Relationship</label>
                                                <input type="text" name="cp_relationship" class="form-control" placeholder="e.g., Son, Daughter, Spouse" required>
                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <label class="form-label fw-bold">Contact Number</label>
                                                <input type="text" name="cp_contact" class="form-control" required>
                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <label class="form-label fw-bold">Email</label>
                                                <input type="email" name="cp_email" class="form-control">
                                            </div>

                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Occupation</label>
                                            <input type="text" name="occupation" class="form-control">
                                        </div>

                                        <div class="text-center mt-4">

                                            <!-- Modal Dialog Scrollable -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDialogScrollable">
                                                Review & Submit
                                            </button>
                                        </div>

                                                <div class="modal fade" id="modalDialogScrollable" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-scrollable">
                                                        <div class="modal-content">

                                                            <div class="modal-header">
                                                                <h5 class="modal-title fw-bold">Important: Read Before Proceeding</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <h5 class="fw-bold">Associate to Regular Member Upgrade Guidelines</h5>
                                                                <p>
                                                                    Before you continue with your membership upgrade, please read and understand the
                                                                    following important reminders:
                                                                </p>

                                                                <ul>
                                                                    <li>You must comply with all requirements set by the organization.</li>
                                                                    <li>All information submitted must be true and accurate.</li>
                                                                    <li>Upgrading to Regular Member provides additional benefits and responsibilities.</li>
                                                                    <li>Regular Members are required to pay monthly dues to maintain active status.</li>
                                                                    <li>Failure to comply with guidelines may delay your membership approval.</li>
                                                                    <li>Regular Membership includes the benefit of the
                                                                        <strong>40,000 Mortuary Assistance Package</strong>, provided monthly fees are paid.
                                                                    </li>
                                                                </ul>

                                                                <p class="mt-3">
                                                                    Scroll down and confirm that you have fully read and understood the guidelines.
                                                                </p>

                                                                <br><br><br><br><br><br><br><br><br>
                                                                <!-- Extra space to demonstrate scrolling -->
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                                                                <!-- Button to proceed after reading -->
                                                                <button type="submit" class="btn btn-primary" id="confirmRead">
                                                                    I Have Read and Understand
                                                                </button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Modal Dialog Scrollable -->


                                    </form>

                </div>
            </div>
        </div>

        <!-- Benefits Column -->
        <div class="col-lg-4 mb-4">

              <div id="membershipCardContainer"></div>
        </div>

    </div>
</section>

<script src="transaction/js/membership.js"></script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
