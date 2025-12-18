<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../database/connection.php';

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Collect POST data safely
$old_pass    = $_POST['old_pass'] ?? '';
$new_pass    = $_POST['new_pass'] ?? '';
$retype_pass = $_POST['retype_pass'] ?? '';
$osca_id     = $_SESSION['osca_id'] ?? ''; // assuming you store OSCA ID in session

// Validate required fields
if (empty($osca_id) || empty($old_pass) || empty($new_pass) || empty($retype_pass)) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
    exit;
}

// Check if new passwords match
if ($new_pass !== $retype_pass) {
    echo json_encode(['status' => 'error', 'message' => 'New passwords do not match']);
    exit;
}

// Verify old password
$stmt = $conn->prepare("SELECT password FROM user_table WHERE osca_id = ?");
$stmt->bind_param("s", $osca_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

$user = $result->fetch_assoc();
$hashed_old = $user['password'];

if (!password_verify($old_pass, $hashed_old)) {
    echo json_encode(['status' => 'error', 'message' => 'Old password is incorrect']);
    exit;
}

// Hash new password
$new_hashed = password_hash($new_pass, PASSWORD_DEFAULT);

// Update password
$update = $conn->prepare("UPDATE user_table SET password = ? WHERE osca_id = ?");
$update->bind_param("ss", $new_hashed, $osca_id);

if ($update->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $update->error]);
}

$stmt->close();
$update->close();
$conn->close();
exit;
?>