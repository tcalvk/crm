<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-muted mb-1">Customer</p>
            <h4 class="mb-0">Statements for <?php echo htmlspecialchars($customer_info['Name']); ?></h4>
        </div>
        <div class="d-flex align-items-center">
            <?php if (!empty($user_info['superuser'])) : ?>
                <a class="btn btn-primary btn-sm mr-2" href="index.php?action=create_statement&customer_id=<?php echo $customer_id; ?>">Create Statement</a>
            <?php endif; ?>
            <a href="../customer/index.php?action=view_customer&customer_id=<?php echo $customer_id; ?>">Back to Customer</a>
        </div>
    </div>

    <?php if (empty($statements)) : ?>
        <div class="alert alert-info">No statements found for this customer.</div>
    <?php else : ?>
        <p class="text-muted">Click a statement to view details or delete it.</p>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Statement #</th>
                    <th scope="col">Created</th>
                    <th scope="col">Due</th>
                    <th scope="col">Total</th>
                    <th scope="col">Paid Date</th>
                    <th scope="col">Property</th>
                    <th scope="col" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($statements as $statement) : 
                $paid = !empty($statement['PaidDate']);
                ?>
                <tr>
                    <td>
                        <a href="../log_statements/index.php?action=view_statement&statement_number=<?php echo $statement['StatementNumber']; ?>">
                            <?php echo htmlspecialchars($statement['StatementNumber']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($statement['CreatedDate']); ?></td>
                    <td><?php echo htmlspecialchars($statement['DueDate'] ?? ''); ?></td>
                    <td>$<?php echo number_format((float) $statement['TotalAmt'], 2); ?></td>
                    <td>
                        <?php if ($paid) : ?>
                            <span class="badge badge-success">Paid <?php echo htmlspecialchars($statement['PaidDate']); ?></span>
                        <?php else : ?>
                            <span class="badge badge-warning">Unpaid</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($statement['PropertyName'] ?? $statement['Address1'] ?? ''); ?></td>
                    <td class="text-right">
                        <a class="mr-2" href="../log_statements/index.php?action=view_statement&statement_number=<?php echo $statement['StatementNumber']; ?>">View</a>
                        <button type="button" class="btn btn-link text-danger p-0 delete-statement" data-toggle="modal" data-target="#confirmDeleteStatement" data-id="<?php echo $statement['StatementNumber']; ?>">
                            Delete
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<form id="deleteStatementForm" method="post" action="index.php">
    <input type="hidden" name="action" value="delete_statement">
    <input type="hidden" name="statement_number" id="deleteStatementId">
</form>

<div class="modal fade" id="confirmDeleteStatement" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteStatementLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteStatementLabel">Delete Statement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this statement? This cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteStatementBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const deleteButtons = document.querySelectorAll('.delete-statement');
    const deleteInput = document.getElementById('deleteStatementId');
    const confirmDeleteBtn = document.getElementById('confirmDeleteStatementBtn');
    const deleteForm = document.getElementById('deleteStatementForm');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            deleteInput.value = this.getAttribute('data-id');
        });
    });

    if (confirmDeleteBtn && deleteForm) {
        confirmDeleteBtn.addEventListener('click', function() {
            deleteForm.submit();
        });
    }
})();
</script>

<?php include '../view/footer.php'; ?>
