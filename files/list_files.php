<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: ../login.php");
    exit;
}

include '../view/header.php';

function format_file_size($size_bytes) {
    $size = (float) $size_bytes;
    if ($size < 1024) {
        return (int) $size . ' B';
    }
    if ($size < 1024 * 1024) {
        return number_format($size / 1024, 2) . ' KB';
    }
    if ($size < 1024 * 1024 * 1024) {
        return number_format($size / (1024 * 1024), 2) . ' MB';
    }
    return number_format($size / (1024 * 1024 * 1024), 2) . ' GB';
}
?>

<main class="mt-4">
    <?php if (!empty($_SESSION['files_error'])) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['files_error']); unset($_SESSION['files_error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['files_message'])) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['files_message']); unset($_SESSION['files_message']); ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Files</h4>
            <small class="text-muted">Upload files and download previously uploaded content.</small>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Upload New File</div>
        <div class="card-body">
            <form action="index.php" method="post" enctype="multipart/form-data" class="form-inline">
                <input type="hidden" name="action" value="upload_file">
                <div class="form-group mr-2 mb-2">
                    <input type="file" name="upload_file" class="form-control-file" required>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Upload</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Uploaded Files</span>
            <?php if ($is_superuser) : ?>
                <form method="get" action="index.php" class="form-inline">
                    <input type="hidden" name="action" value="list_files">
                    <label class="mr-2 mb-0" for="created_by">Uploaded By</label>
                    <select class="form-control form-control-sm mr-2" id="created_by" name="created_by">
                        <option value="">All users</option>
                        <?php foreach ($upload_users as $uploader) : ?>
                            <option value="<?php echo (int) $uploader['userId']; ?>" <?php echo ($created_by_filter !== null && (int) $created_by_filter === (int) $uploader['userId']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(trim(($uploader['firstname'] ?? '') . ' ' . ($uploader['lastname'] ?? ''))); ?> (<?php echo htmlspecialchars($uploader['email']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Filter</button>
                    <?php if ($created_by_filter !== null) : ?>
                        <a class="btn btn-link btn-sm ml-2 p-0" href="index.php?action=list_files">Clear</a>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <?php if ($is_superuser) : ?>
                                <th>File ID</th>
                                <th>Uploaded By</th>
                                <th>Object Name</th>
                                <th>Provider</th>
                                <th>Bucket</th>
                            <?php endif; ?>
                            <th>File Name</th>
                            <th>Content Type</th>
                            <th>Size</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($files)) : ?>
                            <tr>
                                <td colspan="<?php echo $is_superuser ? '10' : '5'; ?>" class="text-center text-muted py-4">No files found.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($files as $file) : ?>
                                <?php
                                    $file_id_value = $file['FileId'] ?? $file['Field'] ?? $file['field'] ?? $file['FileID'] ?? null;
                                    $file_id_value = is_numeric($file_id_value) ? (int) $file_id_value : 0;
                                ?>
                                <tr>
                                    <?php if ($is_superuser) : ?>
                                        <td><?php echo $file_id_value; ?></td>
                                        <td>
                                            <?php echo htmlspecialchars(trim(($file['firstname'] ?? '') . ' ' . ($file['lastname'] ?? ''))); ?>
                                            <div class="small text-muted"><?php echo htmlspecialchars($file['email'] ?? ''); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($file['ObjectName']); ?></td>
                                        <td><?php echo htmlspecialchars($file['Provider']); ?></td>
                                        <td><?php echo htmlspecialchars($file['Bucket']); ?></td>
                                    <?php endif; ?>
                                    <td><?php echo htmlspecialchars($file['FileName'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($file['ContentType']); ?></td>
                                    <td><?php echo htmlspecialchars(format_file_size($file['SizeBytes'] ?? 0)); ?></td>
                                    <td><?php echo htmlspecialchars($file['CreatedAt']); ?></td>
                                    <td>
                                        <?php if ($file_id_value > 0) : ?>
                                            <a class="btn btn-outline-primary btn-sm mr-1" href="index.php?action=download_file&file_id=<?php echo $file_id_value; ?>">Download</a>
                                            <form action="index.php<?php echo $created_by_filter !== null ? '?action=list_files&created_by=' . (int) $created_by_filter : ''; ?>" method="post" class="d-inline">
                                                <input type="hidden" name="action" value="delete_file">
                                                <input type="hidden" name="file_id" value="<?php echo $file_id_value; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this file?');">Delete</button>
                                            </form>
                                        <?php else : ?>
                                            <span class="text-muted">Unavailable</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../view/footer.php'; ?>
