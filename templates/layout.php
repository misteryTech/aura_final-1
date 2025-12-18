<?php
include __DIR__ . '/header.php';
require_once __DIR__ . '/../database/connection.php';

// DO NOT put session_start() here
?>

<?php

// If not logged in → redirect to login
if (!isset($_SESSION['school_id'])) {
    header("Location: ../login.php");
    exit;
}

// If position/session role is missing → redirect
if (!isset($_SESSION['position'])) {
    header("Location: ../login.php");
    exit;
}

// Allowed roles
$allowed_roles = ['student', 'staff', 'treasurer', 'admin'];

// Check if user has allowed role
if (!in_array($_SESSION['position'], $allowed_roles)) {
    header("Location: ../login.php");
    exit;
}

// Session variables
$fullname = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
$position = $_SESSION['position'];
$account  = $_SESSION['account'];
$school_id  = $_SESSION['school_id'];

?>

<body>

<?php include __DIR__ . '/topnav.php'; ?>
<?php include __DIR__ . '/sidenav.php'; ?>

<main id="main" class="main">
    <?= $content; ?>
</main>

<?php include __DIR__ . '/footer.php'; ?>

</body>
</html>
