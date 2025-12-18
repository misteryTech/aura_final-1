<!-- Cashless Payment Modal -->
<div class="modal fade" id="cashlessModal<?= $counter; ?>" tabindex="-1" aria-labelledby="cashlessModalLabel<?= $counter; ?>" >
  <div class="modal-dialog">
    <div class="modal-content">
     <form id="paymentForm<?= $counter; ?>" enctype="multipart/form-data">

        <div class="modal-header">
          <h5 class="modal-title" id="cashlessModalLabel<?= $counter; ?>">Cashless Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <div class="responseBox mt-2"></div>

          <input type="text" name="application_id" value="<?= $row['id']; ?>">
          <input type="text" name="user_id" value="<?= $row['user_id']; ?>">

          <div class="mb-3">
            <label for="screenshot<?= $counter; ?>" class="form-label">Upload Screenshot</label>
            <input class="form-control" type="file" id="screenshot<?= $counter; ?>" name="screenshot" accept="image/*" required>
          </div>

          <div class="mb-3">
            <label for="referenceNo<?= $counter; ?>" class="form-label">Reference Number</label>
            <input type="text" class="form-control" id="referenceNo<?= $counter; ?>" name="reference_no" placeholder="Enter reference number" required>
          </div>



          <div class="mb-3">
            <label for="referenceNo<?= $counter; ?>" class="form-label">Amount</label>
            <input type="number" class="form-control" id="amount<?= $counter; ?>" name="amount" placeholder="Enter amount" required>
          </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Submit Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>
