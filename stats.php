<?php
// stats.php - Displays stats for an individual short code
require_once 'db.php';

$code = isset($_GET['code']) ? trim($_GET['code']) : '';

if (empty($code)) {
    header("Location: index.php?error=" . urlencode("No code provided to view statistics."));
    exit();
}

// Fetch the URL record
$stmt = $conn->prepare("SELECT * FROM urls WHERE short_code = ? LIMIT 1");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: index.php?error=" . urlencode("No statistics found for code '$code'."));
    exit();
}

$data = $result->fetch_assoc();
$stmt->close();

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$baseUrl = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";
$fullShortUrl = $baseUrl . "redirect.php?code=" . htmlspecialchars($data['short_code']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - <?php echo htmlspecialchars($data['short_code']); ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-link-45deg me-1"></i> URL Shortener
        </a>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
                <span class="badge bg-success fs-6">Active Link</span>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 text-primary"><i class="bi bi-bar-chart-line me-2"></i>Statistics Overview</h4>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Metric Card: Total Clicks -->
                    <div class="text-center py-4 bg-light rounded-3 mb-4">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Total Clicks / Redirects</div>
                        <div class="display-3 fw-bold text-primary"><?php echo (int)$data['clicks']; ?></div>
                        <small class="text-muted">Total number of times this short link was opened</small>
                    </div>

                    <!-- Details Table -->
                    <ul class="list-group list-group-flush border-top border-bottom">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="fw-semibold text-muted">Short Code</span>
                            <span class="font-monospace fw-bold fs-5 text-dark"><?php echo htmlspecialchars($data['short_code']); ?></span>
                        </li>
                        <li class="list-group-item py-3">
                            <span class="fw-semibold text-muted d-block mb-1">Full Short URL</span>
                            <div class="input-group">
                                <input type="text" class="form-control" id="shortUrlField" value="<?php echo $fullShortUrl; ?>" readonly>
                                <a href="<?php echo $fullShortUrl; ?>" target="_blank" class="btn btn-outline-primary">Visit Link</a>
                            </div>
                        </li>
                        <li class="list-group-item py-3">
                            <span class="fw-semibold text-muted d-block mb-1">Original Destination URL</span>
                            <a href="<?php echo htmlspecialchars($data['original_url']); ?>" target="_blank" class="text-break text-decoration-none">
                                <?php echo htmlspecialchars($data['original_url']); ?> <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                            </a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="fw-semibold text-muted">Created On</span>
                            <span class="text-dark"><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($data['created_at']))); ?></span>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="index.php" class="btn btn-primary">Done</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>