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
            <h4 class="mb-0">Properties</h4>
            <span class="text-muted d-block">Showing properties for <?php echo $user_info['superuser'] == 1 ? 'all users' : 'your account'; ?>.</span>
        </div>
        <div class="d-flex align-items-center">
            <a class="btn btn-primary btn-sm mr-2" href=".?action=create_property">Create Property</a>
            <button type="button" id="bulkDeleteBtn" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmBulkDelete" disabled>Delete Selected</button>
        </div>
    </div>

    <form id="bulkDeleteForm" method="post" action="index.php">
        <input type="hidden" name="action" value="delete_selected_properties">
        <?php if (empty($properties)) : ?>
            <div class="alert alert-info">No properties found.</div>
        <?php else : ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col"><input type="checkbox" id="selectAll"></th>
                        <th scope="col">Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">City</th>
                        <th scope="col">State</th>
                        <th scope="col">Zip</th>
                        <?php if ($user_info['superuser'] == 1) : ?>
                            <th scope="col">User</th>
                        <?php endif; ?>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $property) : ?>
                    <tr>
                        <td><input type="checkbox" class="row-check" name="selected_properties[]" value="<?php echo $property['PropertyId']; ?>"></td>
                        <td>
                            <a href=".?action=view_property&property_id=<?php echo $property['PropertyId']; ?>">
                                <?php echo htmlspecialchars($property['Name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($property['Address1']); ?></td>
                        <td><?php echo htmlspecialchars($property['City']); ?></td>
                        <td><?php echo htmlspecialchars($property['StateId']); ?></td>
                        <td><?php echo htmlspecialchars($property['Zip']); ?></td>
                        <?php if ($user_info['superuser'] == 1) : ?>
                            <td><?php echo htmlspecialchars($property['OwnerEmail'] ?? ''); ?></td>
                        <?php endif; ?>
                        <td class="text-right">
                            <a href=".?action=view_property&property_id=<?php echo $property['PropertyId']; ?>">View</a>
                            <span class="mx-1 text-muted">|</span>
                            <button type="button" class="btn btn-link text-danger p-0 single-delete" data-toggle="modal" data-target="#confirmDelete" data-id="<?php echo $property['PropertyId']; ?>" data-name="<?php echo htmlspecialchars($property['Name']); ?>">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </form>
</main>

<form id="singleDeleteForm" method="post" action="index.php">
    <input type="hidden" name="action" value="delete_property">
    <input type="hidden" name="property_id" id="deletePropertyId">
</form>

<div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">Delete Property</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="deletePropertyName"></strong>?
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
                <h5 class="modal-title" id="confirmBulkDeleteLabel">Delete Selected Properties</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the selected properties?
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
    const deletePropertyIdInput = document.getElementById('deletePropertyId');
    const deletePropertyName = document.getElementById('deletePropertyName');
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
            deletePropertyIdInput.value = this.getAttribute('data-id');
            deletePropertyName.textContent = this.getAttribute('data-name');
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
