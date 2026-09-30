<?php
// delete.php - Deletes an existing short code record
require_once 'db.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?error=" . urlencode("Invalid request method."));
    exit();
}

$short_code = isset($_POST['short_code']) ? trim($_POST['short_code']) : '';

if (empty($short_code)) {
    header("Location: index.php?error=" . urlencode("Short code is required to delete."));
    exit();
}

// Check if the short code exists before attempting delete
$checkStmt = $conn->prepare("SELECT id FROM urls WHERE short_code = ? LIMIT 1");
$checkStmt->bind_param("s", $short_code);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    $checkStmt->close();
    header("Location: index.php?error=" . urlencode("Short code '$short_code' does not exist."));
    exit();
}
$checkStmt->close();

// Delete the record using prepared statement
$deleteStmt = $conn->prepare("DELETE FROM urls WHERE short_code = ?");
$deleteStmt->bind_param("s", $short_code);

if ($deleteStmt->execute()) {
    $deleteStmt->close();
    $conn->close();
    header("Location: index.php?success=" . urlencode("Short URL '$short_code' was deleted successfully."));
    exit();
} else {
    $errorMsg = $deleteStmt->error;
    $deleteStmt->close();
    $conn->close();
    header("Location: index.php?error=" . urlencode("Delete failed: " . $errorMsg));
    exit();
}
?>