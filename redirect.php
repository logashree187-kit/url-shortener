<?php
// redirect.php - Handles redirection and tracks click counts
require_once 'db.php';

// Check if code parameter is passed
if (!isset($_GET['code']) || empty(trim($_GET['code']))) {
    showError("No short code provided. Please check the URL.");
}

$short_code = trim($_GET['code']);

// Query the database for the short code using a prepared statement
$stmt = $conn->prepare("SELECT id, original_url, clicks FROM urls WHERE short_code = ? LIMIT 1");
$stmt->bind_param("s", $short_code);
$stmt->execute();
$result = $stmt->get_result();

// If record does not exist
if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    showError("The short link '" . htmlspecialchars($short_code) . "' does not exist or has been removed.");
}

$row = $result->fetch_assoc();
$original_url = $row['original_url'];
$stmt->close();

// Increment click count by 1
$updateStmt = $conn->prepare("UPDATE urls SET clicks = clicks + 1 WHERE short_code = ?");
$updateStmt->bind_param("s", $short_code);
$updateStmt->execute();
$updateStmt->close();
$conn->close();

// Perform redirect to destination URL
header("Location: " . $original_url);
exit();

// Helper function to render a user-friendly error page
function showError($message) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Link Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 shadow-sm border-0">
                    <div class="display-1 text-danger fw-bold">404</div>
                    <h3 class="mb-3">Link Not Found</h3>
                    <p class="text-muted mb-4"><?php echo $message; ?></p>
                    <a href="index.php" class="btn btn-primary">Return to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
    exit();
}
?>