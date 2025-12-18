<?php
ob_start();
require_once __DIR__ . '/../database/connection.php';
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12 mx-auto">

            <div class="card">
                <div class="card-body">
                   <h5 class="card-title">Deceased Payment Form</h5>

                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Date Applied</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $counter = 0; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php
                                    $counter++;
                                    $modalId = "viewModal" . $counter;
                                ?>
                                <tr>

                                    <td><?= htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></td>

                                    <td>
                                    <?php
                                        if (empty($row['payment_id'])) {
                                            // No payment record found
                                            echo '<span class="badge bg-secondary">Pending</span>';

                                        } elseif ($row['payment_status'] === 'pending') {
                                            // Payment submitted but not approved
                                            echo '<span class="badge bg-warning text-dark">Processing</span>';

                                        } elseif ($row['payment_status'] === 'completed') {
                                            // Approved by treasurer
                                            echo '<span class="badge bg-success">Paid</span>';

                                        } else {
                                            echo '<span class="badge bg-danger">Unknown</span>';
                                        }
                                    ?>
                                    </td>


                                        <td><?= htmlspecialchars($row['updated_at'] ?? ''); ?></td>

                                        <?php if ($row['payment_status'] != 'pending') : ?>
                                            <td>
                                                <button class="btn btn-success btn-sm me-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cashlessModal<?= $counter; ?>">
                                                    <i class="bi bi-cash"></i> Pay Cashless
                                                </button>
                                            </td>
                                        <?php endif; ?>


                                </tr>

                                <?php  include('payment_modal.php') ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No deceased benefit applications found.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</section>

<script src="transaction/js/payment_transaction.js"></script>
<?php
$stmt->close();
$conn->close();

$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
