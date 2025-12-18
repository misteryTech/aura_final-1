<?php
session_start();
require_once __DIR__ . '/../database/connection.php';

$showBiometricModal = false;

// Check if student has a record in student_biometrics
if (!empty($_SESSION['school_id'])) {

    $student_id = trim($_SESSION['school_id']);

    $stmt = $conn->prepare("
        SELECT 1
        FROM student_biometrics
        WHERE student_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->store_result();

    // Modal should show ONLY IF student + no biometrics
    if ($_SESSION['position'] === 'student' && $stmt->num_rows === 0) {
        $showBiometricModal = true;
    }

    $stmt->close();
}

ob_start();
?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../templates/layout.php';
?>
