<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>
<body onload="statement_overdue_checkbox()">
<script>
    var statement_overdue_notification = <?php echo(json_encode($user_settings['StatementOverdueNotification'])); ?>;
    function statement_overdue_checkbox() {
        if (statement_overdue_notification == 'false') {
            document.getElementById("statement_overdue_notification").checked = false;
        } else {
            document.getElementById("statement_overdue_notification").checked = true;
        }
    }
</script>

<main>
    <br><br>
    <h5>User Information</h5>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">First Name &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_firstname_modal">Edit</a></small></th>
                <th scope="col">Last Name &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_lastname_modal">Edit</a></small></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $user_info['firstname']; ?></td>
                <td><?php echo $user_info['lastname']; ?></td>
            </tr>
        </tbody>
    </table>
    <br><br>
    <h5>Notification Settings</h5>
    <i class="fa-solid fa-circle-info myDIV"></i>
    <div class="hide">
        <small><em>
            <ul>
                <li>When checked, the statement overdue notification setting allows you to receive email notifications when any of your statements are overdue</li>
                <li>The overdue notification days settings specifies how many overdue days are required before a notification to you is generated</li>
                <li>If no number of days is specified in this setting, then no notification can be generated, even if the overdue notification is checked</li>
            </ul>
        </small></em>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Statement Overdue Notification &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_statement_overdue_notification_modal">Edit</a></small></th>
                <th>Statement Overdue Notification Days &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_statement_overdue_notification_days_modal">Edit</small></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="checkbox" id="statement_overdue_notification" disabled></td>
                <td><?php echo $user_settings['StatementOverdueNotificationDays']; ?></td>
            </tr>
        </tbody>
    </table>

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