<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Contracts for <?php echo htmlspecialchars($customer_info['Name']); ?></h4>
        <div class="d-flex align-items-center">
            <a class="btn btn-primary btn-sm mr-2" href=".?action=create_contract&customer_id=<?php echo $customer_id; ?>">Create Contract</a>
            <a href="../customer/index.php?action=view_customer&customer_id=<?php echo $customer_id; ?>">Back to Customer</a>
        </div>
    </div>

    <?php if (empty($contracts)) : ?>
        <div class="alert alert-info">No contracts found for this customer.</div>
    <?php else : ?>
    <form id="bulkDeleteForm" method="post" action="index.php">
        <input type="hidden" name="action" value="delete_selected_contracts">
        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <p class="mb-0 text-muted">Select contracts to delete or view details.</p>
            <button type="button" id="bulkDeleteBtn" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmBulkDelete" disabled>Delete Selected</button>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" id="selectAll"></th>
                    <th scope="col">Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Base Amt</th>
                    <th scope="col">Due Date</th>
                    <th scope="col">Auto Receive</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contracts as $contract) : ?>
                <tr>
                    <td><input type="checkbox" class="row-check" name="selected_contracts[]" value="<?php echo $contract['ContractId']; ?>"></td>
                    <td><?php echo htmlspecialchars($contract['Name']); ?></td>
                    <td><?php echo htmlspecialchars($contract['ContractType']); ?></td>
                    <td><?php echo htmlspecialchars($contract['BaseAmt']); ?></td>
                    <td><?php echo htmlspecialchars($contract['DueDate']); ?></td>
                    <td>
                        <?php if ($contract['StatementAutoReceive'] === 'true') : ?>
                            <span class="badge badge-success">Yes</span>
                        <?php else : ?>
                            <span class="badge badge-secondary">No</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                        <a href=".?action=view_contract&contract_id=<?php echo $contract['ContractId']; ?>">View</a>
                        <span class="mx-1 text-muted">|</span>
                        <button type="button" class="btn btn-link text-danger p-0 single-delete" data-toggle="modal" data-target="#confirmDelete" data-id="<?php echo $contract['ContractId']; ?>" data-name="<?php echo htmlspecialchars($contract['Name']); ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
    <?php endif; ?>
</main>

<form id="singleDeleteForm" method="post" action="index.php">
    <input type="hidden" name="action" value="delete_contract">
    <input type="hidden" name="contract_id" id="deleteContractId">
</form>

<div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">Delete Contract</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="deleteContractName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmBulkDelete" tabindex="-1" role="dialog" aria-labelledby="confirmBulkDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmBulkDeleteLabel">Delete Selected Contracts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the selected contracts?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBulkDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-check');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const singleDeleteButtons = document.querySelectorAll('.single-delete');
    const deleteContractIdInput = document.getElementById('deleteContractId');
    const deleteContractName = document.getElementById('deleteContractName');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    const confirmBulkDeleteBtn = document.getElementById('confirmBulkDeleteBtn');

    function updateBulkDeleteButton() {
        const anyChecked = document.querySelectorAll('.row-check:checked').length > 0;
        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = !anyChecked;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkDeleteButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkDeleteButton);
    });

    singleDeleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            deleteContractIdInput.value = this.getAttribute('data-id');
            deleteContractName.textContent = this.getAttribute('data-name');
        });
    });

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            document.getElementById('singleDeleteForm').submit();
        });
    }

    if (confirmBulkDeleteBtn && bulkDeleteForm) {
        confirmBulkDeleteBtn.addEventListener('click', function() {
            bulkDeleteForm.submit();
        });
    }
})();
</script>

<?php include '../view/footer.php'; ?>
