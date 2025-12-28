<?php
// fingerprint_echo.php

if (isset($_POST['finger_id']) && isset($_POST['action'])) {
    $finger_id = $_POST['finger_id'];
    $action = $_POST['action'];

    echo "Received: Finger ID = $finger_id, Action = $action";
} else {
    echo "No data received";
}
