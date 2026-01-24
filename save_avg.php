<?php
include('database/connection.php');


// Read POST avg temperature
if (isset($_POST['avg'])) {
    $avg = floatval($_POST['avg']); // Convert to float
    // Save $avg to your database here
    echo "Saved: $avg";
} else {
    echo "No avg value received";
}



$stmt = $conn->prepare("INSERT INTO temp_records (avg_temp) VALUES (?)");
$stmt->bind_param("d", $avg);
$stmt->execute();
$stmt->close();
$conn->close();

echo "Recorded: " . $avg . " °C";
?>
