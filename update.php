<?php
// update.php - Updates destination URL for an existing code
require_once 'db.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?error=" . urlencode("Invalid request method."));
    exit();
}

$short_code = isset($_POST['short_code']) ? trim($_POST['short_code']) : '';
$new_url    = isset($_POST['new_url']) ? trim($_POST['new_url']) : '';

// Validation 1: Both fields must be provided
if (empty($short_code) || empty($new_url)) {
    header("Location: index.php?error=" . urlencode("Both Short Code and New Destination URL are required."));
    exit();
}

// Validation 2: URL must be valid
if (!filter_var($new_url, FILTER_VALIDATE_URL)) {
    header("Location: index.php?error=" . urlencode("The new URL is not valid. It must begin with http:// or https://"));
    exit();
}

// Check if short code exists
$checkStmt = $conn->prepare("SELECT id FROM urls WHERE short_code = ? LIMIT 1");
$checkStmt->bind_param("s", $short_code);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    $checkStmt->close();
    header("Location: index.php?error=" . urlencode("The short code '$short_code' does not exist."));
    exit();
}
$checkStmt->close();

// Update destination URL using prepared statement
$updateStmt = $conn->prepare("UPDATE urls SET original_url = ? WHERE short_code = ?");
$updateStmt->bind_param("ss", $new_url, $short_code);

if ($updateStmt->execute()) {
    $updateStmt->close();
    $conn->close();
    header("Location: index.php?success=" . urlencode("URL for '$short_code' updated successfully!"));
    exit();
} else {
    $errorMsg = $updateStmt->error;
    $updateStmt->close();
    $conn->close();
    header("Location: index.php?error=" . urlencode("Update failed: " . $errorMsg));
    exit();
}
?>


