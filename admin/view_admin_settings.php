<?php include '../view/header.php'; ?>

<main class="mt-4">
    <?php if (!empty($_SESSION['admin_error'])) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['admin_error']); unset($_SESSION['admin_error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['admin_message'])) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['admin_message']); unset($_SESSION['admin_message']); ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Admin Settings</h4>
            <small class="text-muted">Superuser-only tools to manage users.</small>
        </div>
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-danger btn-sm mr-2" id="bulkDeleteButton" data-toggle="modal" data-target="#bulkDeleteModal" disabled>Delete Selected</button>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createUserModal">Create User Invite</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Users</span>
            <div class="d-flex align-items-center">
                <form class="form-inline mr-3" method="get" action="index.php">
                    <input type="hidden" name="action" value="view_admin_settings">
                    <input type="text" class="form-control form-control-sm mr-2" name="search" placeholder="Search users" value="<?php echo htmlspecialchars($user_search); ?>">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Search</button>
                    <?php if (!empty($user_search)) : ?>
                        <a class="btn btn-link btn-sm ml-2 p-0" href="index.php?action=view_admin_settings">Clear</a>
                    <?php endif; ?>
                </form>
                <small class="text-muted">Showing <?php echo htmlspecialchars($page); ?> of <?php echo htmlspecialchars($total_pages); ?> pages (<?php echo htmlspecialchars($total_users); ?> total)</small>
            </div>
        </div>
        <div class="card-body p-0">
            <form id="bulkDeleteForm" action="index.php" method="post">
                <input type="hidden" name="action" value="delete_users_bulk">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>User ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Superuser</th>
                                <th>Email Is Validated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="row-check" name="selected_user_ids[]" value="<?php echo htmlspecialchars($user['userId']); ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($user['userId']); ?></td>
                                    <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                                    <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo !empty($user['superuser']) && $user['superuser'] == 1 ? 'Yes' : 'No'; ?></td>
                                    <td><?php echo !empty($user['email_is_validated']) ? 'Yes' : 'No'; ?></td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary btn-sm mr-2 edit-superuser-btn"
                                            data-toggle="modal"
                                            data-target="#editSuperuserModal"
                                            data-user-id="<?php echo htmlspecialchars($user['userId']); ?>"
                                            data-superuser="<?php echo !empty($user['superuser']) ? '1' : '0'; ?>">
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-outline-danger btn-sm delete-user-btn"
                                            data-toggle="modal"
                                            data-target="#deleteUserModal"
                                            data-user-id="<?php echo htmlspecialchars($user['userId']); ?>"
                                            data-name="<?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-outline-danger btn-sm" id="bulkDeleteFooterButton" data-toggle="modal" data-target="#bulkDeleteModal" disabled>Delete Selected</button>
            </div>
            <nav aria-label="User pagination">
                <?php $search_query = !empty($user_search) ? '&search=' . urlencode($user_search) : ''; ?>
                <ul class="pagination mb-0">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="index.php?action=view_admin_settings&page=<?php echo max(1, $page - 1); ?><?php echo $search_query; ?>">Previous</a>
                    </li>
                    <li class="page-item disabled"><span class="page-link">Page <?php echo htmlspecialchars($page); ?> of <?php echo htmlspecialchars($total_pages); ?></span></li>
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="index.php?action=view_admin_settings&page=<?php echo min($total_pages, $page + 1); ?><?php echo $search_query; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>User Invites</span>
            <div class="d-flex align-items-center">
                <form class="form-inline" method="get" action="index.php">
                    <input type="hidden" name="action" value="view_admin_settings">
                    <input type="text" class="form-control form-control-sm mr-2" name="invite_search" placeholder="Search invites" value="<?php echo htmlspecialchars($invite_search); ?>">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Search</button>
                    <?php if (!empty($invite_search)) : ?>
                        <a class="btn btn-link btn-sm ml-2 p-0" href="index.php?action=view_admin_settings">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>User Invite ID</th>
                            <th>Email</th>
                            <th>Invite Code</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invites as $invite) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($invite['UserInviteId']); ?></td>
                                <td><?php echo htmlspecialchars($invite['Email']); ?></td>
                                <td><?php echo htmlspecialchars($invite['InviteCode']); ?></td>
                                <td><?php echo htmlspecialchars($invite['Status']); ?></td>
                                <td><?php echo htmlspecialchars($invite['CreatedAt']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <?php $invite_search_query = !empty($invite_search) ? '&invite_search=' . urlencode($invite_search) : ''; ?>
            <small class="text-muted">Showing <?php echo htmlspecialchars($invites_page); ?> of <?php echo htmlspecialchars($invite_total_pages); ?> pages (<?php echo htmlspecialchars($total_invites); ?> total)</small>
            <nav aria-label="Invite pagination">
                <ul class="pagination mb-0">
                    <li class="page-item <?php echo $invites_page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="index.php?action=view_admin_settings&invite_page=<?php echo max(1, $invites_page - 1); ?><?php echo $invite_search_query; ?>">Previous</a>
                    </li>
                    <li class="page-item disabled"><span class="page-link">Page <?php echo htmlspecialchars($invites_page); ?> of <?php echo htmlspecialchars($invite_total_pages); ?></span></li>
                    <li class="page-item <?php echo $invites_page >= $invite_total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="index.php?action=view_admin_settings&invite_page=<?php echo min($invite_total_pages, $invites_page + 1); ?><?php echo $invite_search_query; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</main>

<!-- Edit Superuser Modal -->
<div class="modal fade" id="editSuperuserModal" tabindex="-1" role="dialog" aria-labelledby="editSuperuserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSuperuserModalLabel">Edit Superuser Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php" method="post">
                <input type="hidden" name="action" value="edit_superuser">
                <input type="hidden" name="target_user_id" id="editSuperuserUserId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_superuser">Superuser</label>
                        <select class="form-control" name="new_superuser" id="new_superuser">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="index.php" method="post">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="target_user_id" id="deleteUserId">
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkDeleteModalLabel">Delete Selected Users</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the selected users?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBulkDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Create User Invite</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="../user_invites/index.php" method="post">
                <input type="hidden" name="action" value="create_user_invite">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_user_email">Email</label>
                        <input type="email" class="form-control" name="new_user_email" id="new_user_email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#editSuperuserModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        var superuser = button.data('superuser');
        $('#editSuperuserUserId').val(userId);
        $('#new_superuser').val(superuser);
    });

    $('#deleteUserModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        var name = button.data('name');
        $('#deleteUserId').val(userId);
        $('#deleteUserName').text(name);
    });

    function updateBulkDeleteButtons() {
        var anyChecked = $('.row-check:checked').length > 0;
        $('#bulkDeleteButton').prop('disabled', !anyChecked);
        $('#bulkDeleteFooterButton').prop('disabled', !anyChecked);
    }

    $('#selectAll').on('change', function () {
        var checked = $(this).is(':checked');
        $('.row-check').prop('checked', checked);
        updateBulkDeleteButtons();
    });

    $(document).on('change', '.row-check', function () {
        updateBulkDeleteButtons();
    });

    $('#confirmBulkDelete').on('click', function () {
        $('#bulkDeleteForm').submit();
    });
</script>

<?php include '../view/footer.php'; ?>
