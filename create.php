<?php
// create.php - Creates a new shortened URL
require_once 'db.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?error=" . urlencode("Invalid request method."));
    exit();
}

// Retrieve and sanitize the submitted URL
$original_url = isset($_POST['original_url']) ? trim($_POST['original_url']) : '';

// 1. Validation: URL cannot be empty
if (empty($original_url)) {
    header("Location: index.php?error=" . urlencode("URL field cannot be empty."));
    exit();
}

// 2. Validation: Must be a well-formed, valid URL
if (!filter_var($original_url, FILTER_VALIDATE_URL)) {
    header("Location: index.php?error=" . urlencode("The URL you entered is invalid. Make sure it includes http:// or https://"));
    exit();
}

// Helper function to generate a random 6-character alphanumeric string
function generateRandomCode($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Generate a guaranteed UNIQUE short code
$short_code = '';
$isUnique = false;
$maxAttempts = 10;
$attempts = 0;

while (!$isUnique && $attempts < $maxAttempts) {
    $generated = generateRandomCode(6);
    $attempts++;

    // Check if this short code already exists in the database
    $checkStmt = $conn->prepare("SELECT id FROM urls WHERE short_code = ? LIMIT 1");
    $checkStmt->bind_param("s", $generated);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        $short_code = $generated;
        $isUnique = true;
    }
    $checkStmt->close();
}

if (!$isUnique) {
    header("Location: index.php?error=" . urlencode("Failed to generate a unique code. Please try again."));
    exit();
}

// Insert the new record into the database using a prepared statement
$stmt = $conn->prepare("INSERT INTO urls (original_url, short_code, clicks) VALUES (?, ?, 0)");
if (!$stmt) {
    header("Location: index.php?error=" . urlencode("Database preparation error: " . $conn->error));
    exit();
}

$stmt->bind_param("ss", $original_url, $short_code);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: index.php?success=" . urlencode("Short URL created successfully! Short code: " . $short_code));
    exit();
} else {
    $errorMsg = $stmt->error;
    $stmt->close();
    $conn->close();
    header("Location: index.php?error=" . urlencode("Database error: " . $errorMsg));
    exit();
}
?>