<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Contacts for <?php echo htmlspecialchars($customer_info['Name']); ?></h4>
        <div class="d-flex align-items-center">
            <a class="btn btn-primary btn-sm mr-2" href="index.php?action=create_contact&customer_id=<?php echo $customer_id; ?>">Create New Contact</a>
            <a href="../customer/index.php?action=view_customer&customer_id=<?php echo $customer_id; ?>">Back to Customer</a>
        </div>
    </div>

    <?php if (empty($contacts)) : ?>
        <div class="alert alert-info">No contacts found for this customer.</div>
    <?php else : ?>
    <form id="bulkDeleteForm" method="post" action="index.php">
        <input type="hidden" name="action" value="delete_selected_contacts">
        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <p class="mb-0 text-muted">Select contacts to delete or view details.</p>
            <button type="button" id="bulkDeleteBtn" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmBulkDelete" disabled>Delete Selected</button>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col"><input type="checkbox" id="selectAll"></th>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Email</th>
                    <th scope="col">Receive Statements</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact) : ?>
                <tr>
                    <td><input type="checkbox" class="row-check" name="selected_contacts[]" value="<?php echo $contact['ContactId']; ?>"></td>
                    <td><?php echo htmlspecialchars($contact['FirstName']); ?></td>
                    <td><?php echo htmlspecialchars($contact['LastName']); ?></td>
                    <td><?php echo htmlspecialchars($contact['Phone']); ?></td>
                    <td><?php echo htmlspecialchars($contact['Email']); ?></td>
                    <td>
                        <?php if (!empty($contact['ReceiveStatements'])) : ?>
                            <span class="badge badge-success">Yes</span>
                        <?php else : ?>
                            <span class="badge badge-secondary">No</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                        <a href="index.php?action=view_contact&contact_id=<?php echo $contact['ContactId']; ?>">View</a>
                        <span class="mx-1 text-muted">|</span>
                        <button type="button" class="btn btn-link text-danger p-0 single-delete" data-toggle="modal" data-target="#confirmDelete" data-id="<?php echo $contact['ContactId']; ?>" data-name="<?php echo htmlspecialchars($contact['FirstName'] . ' ' . $contact['LastName']); ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
    <?php endif; ?>
</main>

<form id="singleDeleteForm" method="post" action="index.php">
    <input type="hidden" name="action" value="delete_contact">
    <input type="hidden" name="contact_id" id="deleteContactId">
</form>

<div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">Delete Contact</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="deleteContactName"></strong>?
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
                <h5 class="modal-title" id="confirmBulkDeleteLabel">Delete Selected Contacts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the selected contacts?
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
    const deleteContactIdInput = document.getElementById('deleteContactId');
    const deleteContactName = document.getElementById('deleteContactName');
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
            deleteContactIdInput.value = this.getAttribute('data-id');
            deleteContactName.textContent = this.getAttribute('data-name');
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
