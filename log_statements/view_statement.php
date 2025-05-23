<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>
<body onload="load_functions()">
<script>
    var display = <?php echo(json_encode($display)); ?>;
    var display_markpaid = <?php echo(json_encode($display_markpaid)); ?>
    
    function load_functions() {
        hide_elements();
        hide_markpaid();
    }

    function hide_elements() {
        if (display == 1) {
            document.getElementById('action_dropdown').style.visibility = 'visible';
            document.getElementById('write_off_alert').style.visibility = 'hidden';
        } else {
            document.getElementById('action_dropdown').style.visibility = 'hidden';
            document.getElementById('write_off_alert').style.visibility = 'visible';
        }
    }

    function hide_markpaid() {
        if (display_markpaid == 1) {
            document.getElementById('mark_paid_button').style.visibility = 'visible';
        } else {
            document.getElementById('mark_paid_button').style.visibility = 'hidden';
        }
    }
</script>


<main>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #FFFFFF;">
    Statement <?php echo $statement['StatementNumber']; ?><br>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
            <li id="action_dropdown" class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <button type="button" class="dropdown-item" id="mark_paid_button" data-toggle="modal" data-target="#exampleModal">Mark as Paid</button>
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="clear_paid_date">
                        <input type="hidden" name="statement_number" value="<?php echo $statement['StatementNumber']; ?>">
                        <input type="submit" value="Clear Paid Date" class="dropdown-item">
                    </form>
                    <!-- <button type="button" class="dropdown-item" data-toggle="modal" data-target="#writeoffModal">Write Off Statement</button> -->
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
                <th scope="col">Contract</th>
                <th scope="col">Customer Name</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="../contract/index.php?action=view_contract&contract_id=<?php echo $statement['ContractId']; ?>"><?php echo $statement['ContractName']; ?></a></td>
                <td><a href="../customer/index.php?action=view_customer&customer_id=<?php echo $statement['CustomerId']; ?>"><?php echo $statement['CustomerName']; ?></a></td>
            </tr>
            <tr>
                <th scope="col">Sent Date</th>
                <th scope="col">Base Amount</th>
            </tr>
            <tr>
            <td><?php echo $statement['CreatedDate']; ?></td>
            <td>$<?php echo $statement['BaseAmt']; ?></td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th scope="col">Due Date</th>
                <th scope="col">CAM</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $statement['DueDate']; ?></td>
                <td>$<?php echo $statement['CAM']; ?></td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th scope="col">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
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
            <tr>
                <th scope="col"></th>
                <th scope="col">Payment Amount</th>
            </tr>
            <tr>
                <td></td>
                <td><?php echo $statement['PaymentAmount']; ?></td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <h5>Automation</h5>
    <i class="fa-solid fa-circle-info myDIV"></i>
    <div class="hide">
        <small><em>
            <ul>
                <li>Statement Auto Receive: This setting will allow statements to be automatically marked as paid in full upon statement due date. To enable for a contract's statements, navigate to any given contract.</li>
            </ul>
        </small></em>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Statement Auto Receive</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $statement_auto_receive; ?></td>
                <td></td>
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
                        <div class="form-group">
                            <label for="payment_amount" class="col-form-label">Payment Amount</label>
                            <span class="input-group-addon">$</span>
                            <input type="number" value="<?php echo $statement['PaymentAmount']; ?>" min="0" step="0.01" name="payment_amount" id="payment_amount" class="form-control currency">
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