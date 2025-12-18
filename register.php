<?php
session_start();
// If user already has a session, redirect them
if (isset($_SESSION['user_id'])) {
    // You can redirect based on their role/position if needed
    if ($_SESSION['position'] === 'Admin') {
        header("Location: admin/dashboard.php");
        exit;
    } else {
        header("Location: member/dashboard.php");
        exit;
    }
}


    include('templates/header.php');

?>


  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-8 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.png" alt="">
                  <span class="d-none d-lg-block">Member Registration</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>
                            <form id="registerForm" class="row g-3 needs-validation" novalidate>
                            <!-- Row 1 -->
                             <div id="responseBox" class="mt-3 text-success"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="oscaId" class="form-label">School ID.</label>
                                    <input type="text" name="school_id" class="form-control" id="school_id" required>
                                    <div class="invalid-feedback">Please enter your OSCA ID!</div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" id="firstName" required>
                                    <div class="invalid-feedback">Please enter your first name!</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" id="lastName" required>
                                    <div class="invalid-feedback">Please enter your last name!</div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="birthDate" class="form-label">Birth Date</label>
                                    <input type="date" name="birth_date" class="form-control" id="birthDate" required>
                                    <div class="invalid-feedback">Please enter your birth date!</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="placeBirth" class="form-label">Place of Birth</label>
                                    <input type="text" name="place_birth" class="form-control" id="placeBirth" required>
                                    <div class="invalid-feedback">Please enter your place of birth!</div>
                                 </div>


                            </div>


                            <!-- Row 2 -->

                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" id="password" required>
                                <div class="invalid-feedback">Please enter your password!</div>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required>
                                <label class="form-check-label" for="acceptTerms">I agree and accept the <a href="#">terms and conditions</a></label>
                                <div class="invalid-feedback">You must agree before submitting.</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Create Account</button>

                            </div>

                            <div class="col-12">
                                <p class="small mb-0">Already have an account? <a href="login.php">Log in</a></p>
                            </div>
                            </form>

                </div>
              </div>

              <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                 Record <a href="#">Management System</a>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->




<?php
    include('templates/footer.php');

?>
  <script src="assets/js/source.js"></script>