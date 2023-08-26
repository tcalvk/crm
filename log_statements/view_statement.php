<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>
<body onload="hide_elements()">
<script>
    var display = <?php echo(json_encode($display)); ?>;
    function hide_elements() {
        if (display == 1) {
            document.getElementById('action_dropdown').style.visibility = 'visible';
            document.getElementById('write_off_alert').style.visibility = 'hidden';
        } else {
            document.getElementById('action_dropdown').style.visibility = 'hidden';
            document.getElementById('write_off_alert').style.visibility = 'visible';
        }
    }
</script>


<main>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #FFFFFF;">
    Statement <?php echo $statement['StatementNumber']; ?><br>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
        <li id="action_dropdown" class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                <button type="button" class="dropdown-item" data-toggle="modal" data-target="#exampleModal">Mark as Paid</button>
                <form action="index.php" method="post">
                    <input type="hidden" name="action" value="clear_paid_date">
                    <input type="hidden" name="statement_number" value="<?php echo $statement['StatementNumber']; ?>">
                    <input type="submit" value="Clear Paid Date" class="dropdown-item">
                </form>
                <button type="button" class="dropdown-item" data-toggle="modal" data-target="#writeoffModal">Write Off Statement</button>
            </div>
        </li>
        </ul>
    </div>
    </nav>
    <div class="alert alert-danger" role="alert" id="write_off_alert">
        This statement has been written off
    </div>    
    <br><br>
    <h5>Statement Information</h5>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Customer Name</th>
                <th scope="col">Property</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="../customer/index.php?action=view_customer&customer_id=<?php echo $statement['CustomerId']; ?>"><?php echo $statement['CustomerName']; ?></a></td>
                <td><?php echo $statement['Property']; ?></td>
            </tr>
            <tr>
                <th scope="col">Sent Date</th>
                <th scope="col">Total Amount</th>
            </tr>
            <tr>
            <td><?php echo $statement['CreatedDate']; ?></td>
            <td>$<?php echo $statement['TotalAmt']; ?></td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <h5>Payment Information</h5>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Status</th>
                <th scope="col">Paid Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <td><?php echo $statement['Status']; ?></td>
            <td><?php echo $statement['PaidDate']; ?></td>
            </tr>
        </tbody>
    </table>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Mark Statement as Paid</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="mark_paid">
                        <input type="hidden" name="statement_number" value="<?php echo $statement['StatementNumber']; ?>">
                        <div class="form-group">
                            <label for="paid_date" class="col-form-label">Date Paid:</label>
                            <input type="date" class="form-control" id="paid_date" name="paid_date">
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

    <div class="modal fade" id="writeoffModal" tabindex="-1" role="dialog" aria-labelledby="writeoffModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="writeoffModalLabel">Write Off Statement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Alert: You are about to write off this statement. This action will remove this unpaid statement from all reports, and this customer's contract will revert to good standing. If there is a paid date listed, that date will be cleared. This action cannot be undone.</p>
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="write_off">
                        <input type="hidden" name="statement_number" value="<?php echo $statement['StatementNumber']; ?>">
                </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-primary" value="Save">
                    </form>
                    </div>
            </div>
        </div>
    </div>

</main>

<?php include '../view/footer.php'; ?>