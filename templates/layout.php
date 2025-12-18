<?php include __DIR__ . '/header.php';
require_once __DIR__ . '/../database/connection.php';?>

<?php

session_start();

// If not logged in → redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// If position/session role is missing → redirect
if (!isset($_SESSION['position'])) {
    header("Location: ../login.php");
    exit;
}

// Allowed roles
$allowed_roles = ['member', 'staff', 'treasurer', 'president'];

// Check if user has allowed role
if (!in_array($_SESSION['position'], $allowed_roles)) {
    header("Location: ../login.php");
    exit;
}

// Session variables for use
$fullname = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
$position = $_SESSION['position'];
$chapter  = $_SESSION['chapter'];
$account  = $_SESSION['account'];
$osca_id  = $_SESSION['osca_id'];

?>
<body>

<?php include __DIR__ . '/topnav.php'; ?>
<?php include __DIR__ . '/sidenav.php'; ?>

<main id="main" class="main">
    <?php echo $content; ?>
</main>

<?php include __DIR__ . '/footer.php'; ?>


</body>
</html>
