<?php
session_start();
require_once __DIR__ . "/../../../database/connection.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit;
}

$user_id             = $_POST['user_id'] ?? null;
$deceased_benefit_id = $_POST['application_id'] ?? null;
$transaction_type    = 'online';
$amount              = $_POST['amount'] ?? null;
$payment_method      = 'Cashless';
$reference_no        = $_POST['reference_no'] ?? null;
$remarks             = 'Cashless payment';
$payment_status      = 'pending';
$transact_by         = $_SESSION['fullname'] ?? 'System';

if (!$user_id || !$deceased_benefit_id || !$amount || !$reference_no) {
    echo json_encode([
        "status" => "error",
        "message" => "All required fields must be filled."
    ]);
    exit;
}

/* ===============================
   PHOTO RECEIPT UPLOAD
================================ */
$uploadDir = __DIR__ . "/../../../assets/uploads/payment_receipts/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$receiptPhoto = null;

if (!empty($_FILES['screenshot']['name'])) {

    $fileTmp  = $_FILES['screenshot']['tmp_name'];
    $fileExt  = strtolower(pathinfo($_FILES['screenshot']['name'], PATHINFO_EXTENSION));
    $allowed  = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($fileExt, $allowed)) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid image format."
        ]);
        exit;
    }

    $receiptPhoto = 'receipt_' . time() . '_' . uniqid() . '.' . $fileExt;
    move_uploaded_file($fileTmp, $uploadDir . $receiptPhoto);
}

/* ===============================
   INSERT PAYMENT
================================ */
$stmt = $conn->prepare("
    INSERT INTO payments (
        user_id,
        deceased_benefit_id,
        transaction_type,
        amount,
        payment_status,
        payment_method,
        reference_no,
        receipt_photo,
        remarks,
        transact_by
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iisdssssss",
    $user_id,
    $deceased_benefit_id,
    $transaction_type,
    $amount,
    $payment_status,
    $payment_method,
    $reference_no,
    $receiptPhoto,
    $remarks,
    $transact_by
);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Payment and receipt uploaded successfully."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to save payment."
    ]);
}

$stmt->close();
$conn->close();
