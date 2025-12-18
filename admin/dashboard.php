<?php
session_start();
ob_start(); // Capture page content
?>

<section class="section">
        <div class="row">
        <!-- Membership Upgrade Announcement -->
        <?php if ($_SESSION['account'] !== "Regular") : ?>
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <h3 class="card-title fw-bold">Upgrade to Regular Membership</h3>
                        <p class="mb-3">
                            As part of our community growth, we are now offering all Associate Members
                            the opportunity to upgrade into <strong>Regular Members</strong>.
                        </p>

                        <div class="alert alert-primary">
                            Enjoy more benefits, full voting rights, and exclusive member privileges.
                        </div>

                        <a href="member_upgrade.php" class="btn btn-primary btn-lg mt-2">
                            Upgrade Now
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>


    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>