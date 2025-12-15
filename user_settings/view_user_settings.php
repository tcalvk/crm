<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';

$statement_overdue_enabled = $user_settings['StatementOverdueNotification'] === 'true';
?>

<main class="mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">User Settings</h4>
            <small class="text-muted">Manage your profile details and notifications.</small>
        </div>
        <div class="d-flex align-items-center">
            <?php if (!empty($user_info['superuser']) && $user_info['superuser'] == 1) : ?>
                <a class="btn btn-secondary btn-sm mr-2" href="/admin/index.php?action=view_admin_settings">Admin</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">User Information</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <p class="text-muted text-uppercase small mb-1">First Name</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <span><?php echo htmlspecialchars($user_info['firstname']); ?></span>
                        <a href="#" data-toggle="modal" data-target="#edit_firstname_modal">Edit</a>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="text-muted text-uppercase small mb-1">Last Name</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <span><?php echo htmlspecialchars($user_info['lastname']); ?></span>
                        <a href="#" data-toggle="modal" data-target="#edit_lastname_modal">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Notification Settings</div>
        <div class="card-body">
            <div class="alert alert-info small">
                <ul class="mb-0">
                    <li>Enable overdue notifications to get an email when a statement goes past due.</li>
                    <li>Set the number of days overdue before the notification is sent.</li>
                    <li>If no number of days is set, no notification will be generated.</li>
                </ul>
            </div>
            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <p class="text-muted text-uppercase small mb-1">Statement Overdue Notification</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <input type="checkbox" id="statement_overdue_notification" disabled <?php echo $statement_overdue_enabled ? 'checked' : ''; ?>>
                        <a href="#" data-toggle="modal" data-target="#edit_statement_overdue_notification_modal">Edit</a>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <p class="text-muted text-uppercase small mb-1">Notification Days</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <span><?php echo htmlspecialchars($user_settings['StatementOverdueNotificationDays']); ?></span>
                        <a href="#" data-toggle="modal" data-target="#edit_statement_overdue_notification_days_modal">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!---->
    <!---->
    <!--Hidden Modals below-->
    <!---->
    <!---->
    <div class="modal fade" id="edit_firstname_modal" tabindex="-1" role="dialog" aria-labelledby="edit_firstname_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_firstname_modal_label">Edit First Name</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_firstname">
                        <div class="form-group">
                            <label for="new_firstname" class="col-form-label">New First Name:</label>
                            <input type="text" name="new_firstname" id="new_firstname">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Save">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_lastname_modal" tabindex="-1" role="dialog" aria-labelledby="edit_lastname_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_lastname_modal_label">Edit Last Name</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_lastname">
                        <div class="form-group">
                            <label for="new_lastname" class="col-form-label">New Last Name:</label>
                            <input type="text" name="new_lastname" id="new_lastname">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Save">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_statement_overdue_notification_modal" tabindex="-1" role="dialog" aria-labelledby="edit_statement_overdue_notification_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_statement_overdue_notification_modal_label">Edit Statement Overdue Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_statement_overdue_notification">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="new_statement_overdue_notification" id="new_statement_overdue_notification">
                            <label class="form-check-label" for="new_statement_overdue_notification">Statement Overdue Notifications</label>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Save">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_statement_overdue_notification_days_modal" tabindex="-1" role="dialog" aria-labelledby="edit_statement_overdue_notification_days_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_statement_overdue_notification_days_modal_label">Edit Statement Overdue Notification Days</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_statement_overdue_notification_days">
                        <div class="form-group">
                            <label for="new_statement_overdue_notification_days">Number of Days</label>
                            <input type="number" name="new_statement_overdue_notification_days" id="new_statement_overdue_notification_days">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Save">
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>




<?php include '../view/footer.php'; ?>
