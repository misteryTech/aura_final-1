<?php
session_start();

$school_id = $_SESSION['school_id'] ?? '';
ob_start(); // Capture page content
?>

<div class="pagetitle">
  <h1>Profile</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">Home</a></li>
      <li class="breadcrumb-item">Members</li>
      <li class="breadcrumb-item active">Profile</li>
    </ol>
  </nav>
</div>

<section class="section profile">
  <div class="row">
    <!-- Left Column: Profile Card -->
    <div class="col-xl-4">


      <div class="card">
        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
          <h1><?= htmlspecialchars($school_id); ?></h1>

        </div>
      </div>
    </div>

    <!-- Right Column: Tabs -->
    <div class="col-xl-8">
      <div class="card">
        <div class="card-body pt-3">
          <!-- Tabs -->
          <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview" type="button" role="tab">Overview</button>
            </li>


             <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-biometrics" type="button" role="tab">Biometrics</button>
            </li>


            <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit" type="button" role="tab">Edit Profile</button>
            </li>

             <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-pass" type="button" role="tab">Change Password</button>
            </li>



          </ul>

          <div class="tab-content pt-2">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active profile-overview" id="profile-overview" role="tabpanel">
              <h5 class="card-title">Profile Details</h5>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Full Name</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_firstname . ' ' . $member_lastname); ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Birth Date</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_birthdate); ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Civil  Status</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_civil_status); ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Address</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_address); ?></div>
              </div>
              <hr>
              <h5 class="card-title">Pension Details</h5>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Pensioner</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_pensioner); ?></div>
              </div>
               <div class="row">
                <div class="col-lg-3 col-md-4 label">Pension Details</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($member_pensioner_details); ?></div>
              </div>

              <?php if ($member_account ==="Associate") : ?>
               <hr>
              <h5 class="card-title">Contact Person information</h5>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Fullname</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($cp_fullname); ?></div>
              </div>
               <div class="row">
                <div class="col-lg-3 col-md-4 label">Relationship</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($cp_relationship); ?></div>
              </div>

                 <div class="row">
                <div class="col-lg-3 col-md-4 label">Contact</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($cp_contact); ?></div>
              </div>


                 <div class="row">
                <div class="col-lg-3 col-md-4 label">Email</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($cp_email); ?></div>
              </div>

                 <div class="row">
                <div class="col-lg-3 col-md-4 label">Occupation</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($cp_occupation); ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Date Applied</div>
                <div class="col-lg-9 col-md-8"><?= htmlspecialchars($date_added); ?></div>
              </div>

              <?php endif; ?>


            </div>

             <div class="tab-pane fade profile-biometrics pt-3" id="profile-biometrics" role="tabpanel">
              <p>Biometrics.</p>
                            <form id="updateBiometrics" class="row g-3 needs-validation" novalidate>
                <!-- Response Box -->
                <div id="responseBox" class="mt-3 text-success"></div>

                <!-- Row 1: Pincode -->
                <div class="row">
                  <div class="col-md-6">
                    <label for="pincode" class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control" id="pincode" required>
                    <div class="invalid-feedback">Please enter your pincode!</div>
                  </div>
                </div>

                <!-- Row 2: Facial Recognition -->
                <div class="row">
                  <div class="col-md-6">
                    <label for="facialRecognition" class="form-label">Facial Recognition</label>
                    <input type="text" name="facial_recognition" class="form-control" id="facialRecognition" required>
                    <div class="invalid-feedback">Please enter your facial recognition data!</div>
                  </div>
                </div>

                <!-- Row 3: Biometrics -->
                <div class="row">
                  <div class="col-md-6">
                    <label for="biometrics" class="form-label">Biometrics</label>
                    <input type="text" name="biometrics" class="form-control" id="biometrics" required>
                    <div class="invalid-feedback">Please enter your biometrics!</div>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12">
                  <button class="btn btn-primary w-100" type="submit">Update Account</button>
                </div>
              </form>
          </div>


            <!-- Edit Profile Tab -->
          <div class="tab-pane fade profile-edit pt-3" id="profile-edit" role="tabpanel">
              <p>Edit profile form goes here.</p>
                <form id="updateProfile" class="row g-3 needs-validation" novalidate>
                            <!-- Row 1 -->
                             <div id="responseBox" class="mt-3 text-success"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="oscaId" class="form-label">OSCA ID No.</label>
                                    <input type="text" value="<?= $osca_id ?>" name="osca_id" class="form-control" id="oscaId" required>
                                    <div class="invalid-feedback">Please enter your OSCA ID!</div>
                                </div>

                                <div class="col-md-6">
                                  <label for="Chapter" class="form-label">Chapter</label>
                                   <select name="chapter" class="form-select" id="chapter" required>
                                    <option value="<?= $member_chapter ?>"><?= $member_chapter ?></option>
                                    <option value="Chapter1">Chapter 1</option>

                                    </select>
                                    <div class="invalid-feedback">Please select Chapter!</div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" name="first_name" value="<?= $member_firstname ?>" class="form-control" id="firstName" required>
                                    <div class="invalid-feedback">Please enter your first name!</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" name="last_name" value="<?= $member_lastname ?>" class="form-control" id="lastName" required>
                                    <div class="invalid-feedback">Please enter your last name!</div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="birthDate" class="form-label">Birth Date</label>
                                    <input type="date" name="birth_date" value="<?= $member_birthdate ?>" class="form-control" id="birthDate" required>
                                    <div class="invalid-feedback">Please enter your birth date!</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="placeBirth" class="form-label">Place of Birth</label>
                                    <input type="text" name="place_birth" value="<?= $member_address ?>" class="form-control" id="placeBirth" required>
                                    <div class="invalid-feedback">Please enter your place of birth!</div>
                                 </div>
                            </div>

                            <div class="col-md-4">
                                <label for="civilStatus" class="form-label">Civil Status</label>
                                <input type="text" name="civil_status" value="<?= $member_civil_status ?>" class="form-control" id="civilStatus" required>
                                <div class="invalid-feedback">Please enter your civil status!</div>
                            </div>

                            <!-- Row 2 -->

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="pensioner" class="form-label">Pensioner</label>
                                    <select name="pensioner" class="form-select" id="pensioner" required>
                                    <option value="<?= $member_pensioner ?>"><?= $member_pensioner ?></option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                    </select>
                                    <div class="invalid-feedback">Please select pensioner status!</div>
                                </div>


                                <div class="col-md-6">
                                    <label for="pensionDetails" class="form-label">Pension Details</label>
                                    <input type="text" value="<?= $member_pensioner_details ?>" name="pension_details" class="form-control" id="pensionDetails">
                                </div>


                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email ( Guardian / Contact Person )</label>
                                <input type="email" name="email" value="<?= $member_email ?>" class="form-control" id="email" required>
                                <div class="invalid-feedback">Please enter a valid email address!</div>
                            </div>



                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Update Account</button>

                            </div>

                            </form>
          </div>
            <div class="tab-pane fade profile-change-pass pt-3" id="profile-change-pass" role="tabpanel">
                              <form id="updatePassword" class="row g-3 needs-validation" novalidate>
                      <div id="responseBoxes" class="mt-3 text-success"></div>

                      <div class="row">
                      <!-- Old Password -->

                      <div class="col-md-6">
                        <label for="old_pass" class="form-label">Old Password</label>
                        <input type="password" name="old_pass" class="form-control" id="old_pass" required>
                        <div class="invalid-feedback">Please enter your old password.</div>
                      </div>
                     </div>
                      <!-- New Password -->
                      <div class="col-md-6">
                        <label for="new_pass" class="form-label">New Password</label>
                        <input type="password" name="new_pass" class="form-control" id="new_pass" required>
                        <div class="invalid-feedback">Please enter your new password.</div>
                      </div>

                      <!-- Retype Password -->
                      <div class="col-md-6">
                        <label for="retype_pass" class="form-label">Retype New Password</label>
                        <input type="password" name="retype_pass" class="form-control" id="retype_pass" required>
                        <div class="invalid-feedback">Please retype your new password.</div>
                      </div>

                      <!-- Optional: Submit button -->
                      <div class="col-12">
                        <button class="btn btn-primary" type="submit">Update Password</button>
                      </div>
                    </form>

          </div>



          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>

  <script src="transaction/js/updateBiometrics.js"></script>