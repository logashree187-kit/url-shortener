<?php
// index.php - Main dashboard
require_once 'db.php';

// Fetch all shortened URLs from the database ordered by newest first
$sql = "SELECT * FROM urls ORDER BY created_at DESC";
$result = $conn->query($sql);

// Helper function to build dynamic base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$baseUrl = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-link-45deg me-1"></i> URL Shortener
        </a>
    </div>
</nav>

<div class="container my-4">

    <!-- Flash Feedback Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Section 1: Create URL Form -->
    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary"><i class="bi bi-plus-circle me-2"></i>Create New Short URL</h5>
        </div>
        <div class="card-body p-4">
            <form action="create.php" method="POST">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-globe"></i></span>
                    <input type="url" name="original_url" class="form-control form-control-lg" placeholder="https://example.com/very-long-link-address" required>
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="bi bi-scissors me-1"></i> Shorten URL
                    </button>
                </div>
                <div class="form-text mt-2 text-muted">Enter a valid full web address including <code>http://</code> or <code>https://</code>.</div>
            </form>
        </div>
    </div>

    <!-- Section 2: Manage & View URLs Table -->
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-secondary"><i class="bi bi-list-ul me-2"></i>Existing Shortened Links</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Original URL</th>
                            <th>Short Code</th>
                            <th>Short URL</th>
                            <th class="text-center">Clicks</th>
                            <th>Created Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php $fullShortUrl = $baseUrl . "redirect.php?code=" . htmlspecialchars($row['short_code']); ?>
                                <tr>
                                    <td class="ps-4">
                                        <a href="<?php echo htmlspecialchars($row['original_url']); ?>" target="_blank" class="text-decoration-none url-truncate" title="<?php echo htmlspecialchars($row['original_url']); ?>">
                                            <?php echo htmlspecialchars($row['original_url']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary font-monospace"><?php echo htmlspecialchars($row['short_code']); ?></span>
                                    </td>
                                    <td>
                                        <a href="<?php echo $fullShortUrl; ?>" target="_blank" class="fw-semibold text-decoration-none">
                                            <?php echo $fullShortUrl; ?>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark rounded-pill badge-clicks"><?php echo (int)$row['clicks']; ?></span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo htmlspecialchars(date('M d, Y H:i', strtotime($row['created_at']))); ?></small>
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- Stats Link -->
                                        <a href="stats.php?code=<?php echo urlencode($row['short_code']); ?>" class="btn btn-sm btn-outline-info me-1" title="View Statistics">
                                            <i class="bi bi-graph-up"></i>
                                        </a>
                                        <!-- Edit Modal Trigger Button -->
                                        <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#editModal" 
                                                data-code="<?php echo htmlspecialchars($row['short_code']); ?>" 
                                                data-url="<?php echo htmlspecialchars($row['original_url']); ?>" title="Edit Destination">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <!-- Delete Modal Trigger Button -->
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                data-code="<?php echo htmlspecialchars($row['short_code']); ?>" title="Delete URL">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No short URLs created yet. Use the form above to add your first one!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Update Short URL Target -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="update.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Update Destination URL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Short Code</label>
                    <input type="text" name="short_code" id="modal_edit_code" class="form-control bg-light" readonly required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Original URL</label>
                    <input type="url" name="new_url" id="modal_edit_url" class="form-control" placeholder="https://new-destination.com" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Delete Confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="delete.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the short URL for code: <strong id="modal_delete_code_display" class="font-monospace"></strong>?</p>
                <input type="hidden" name="short_code" id="modal_delete_code">
                <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle"></i> This action cannot be reversed.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Confirm Delete</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap 5 JavaScript Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Simple Script to Transfer Row Data into Modals -->
<script>
    // Edit Modal Data Passing
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const code = button.getAttribute('data-code');
            const url = button.getAttribute('data-url');
            document.getElementById('modal_edit_code').value = code;
            document.getElementById('modal_edit_url').value = url;
        });
    }

    // Delete Modal Data Passing
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const code = button.getAttribute('data-code');
            document.getElementById('modal_delete_code').value = code;
            document.getElementById('modal_delete_code_display').textContent = code;
        });
    }
</script>
</body>
</html>