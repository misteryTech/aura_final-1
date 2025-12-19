<div class="modal fade" id="biometricModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title">Biometric Registration Required</h5>
      </div>

      <!-- Body -->
      <div class="modal-body text-center">
        <p>
          Our records show that you have not yet registered your biometrics with the AURA system.
        </p>
        <p class="text-danger fw-bold">
          Biometric registration is required to fully access the automated attendance system.
        </p>

        <!-- Form aligned inside body -->
        <form id="requestForm" class="mt-3">
          <!-- Student ID -->
          <div class="mb-3">
            <label for="student_id" class="form-label">Student ID</label>
            <input type="text" id="student_id" name="student_id"
                   value="<?= $student_id ?>"
                   class="form-control text-center" readonly>
          </div>

          <!-- Remarks -->
          <div class="mb-3">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea id="remarks" name="remarks"
                      class="form-control" rows="3"
                      placeholder="Enter any remarks here..."></textarea>
          </div>

        <button type="submit" class="btn btn-success">
          Request Biometrics Now
        </button>

        <a href="../logout.php"><div class="btn btn-danger">Logout</div></a>
        </form>
      </div>

      <!-- Footer -->
      <div class="modal-footer justify-content-center">

      </div>

    </div>
  </div>
</div>