<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<body onload="load_functions()">
<script>
    var contract_type = <?php echo(json_encode($contract_info['ContractType'])); ?>;
    var statement_auto_receive = <?php echo(json_encode($contract_info['StatementAutoReceive'])); ?>;

    function load_functions() {
        hide_elements();
        statement_auto_receive_view();
    }
    function hide_elements() {
        if (contract_type == 'Evergreen') {
            document.getElementById('accordion').style.visibility = 'visible';
        } else {
            document.getElementById('accordion').style.visibility = 'hidden';
        }
    }
    function statement_auto_receive_view() {
        if (statement_auto_receive == 'false') {
            document.getElementById("statement_auto_receive").checked = false;
        } else {
            document.getElementById("statement_auto_receive").checked = true;
        }
    }
</script>

<main>
    <br><br>
    <h5>Basic Information</h5>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Contract Name</th>
                <th scope="col">Contract Type</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $contract_info['Name']; ?></td>
                <td><?php echo $contract_info['ContractType']; ?></td>
            </tr>
        </tbody>
        <thead>
            <th scope="col">Customer</th>
            <th scope="col">Property</th>
        </thead>
        <tbody>
            <td><a href="../customer/index.php?action=view_customer&customer_id=<?php echo $contract_info['CustomerId']; ?>"><?php echo $contract_info['CustomerName']; ?></a></td>
            <td><?php echo $contract_info['PropertyName']; ?></td>
        </tbody>
        <thead>
            <th scope="col">Company</th>
            <th scope="col">Statement Auto Receive &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_statementautoreceive_modal">Edit</a></small></th></th>
        </thead>
        <tbody>
            <td><?php echo $contract_info['CompanyName']; ?></td>
            <td><input type="checkbox" id="statement_auto_receive" disabled></td>        
        </tbody>
    </table>
    <br><br>
    <h5>Billing Information</h5>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Base Amount (Current Term)</th>
                <th scope="col">CAM</th>
            </tr>
        </thead>
        <tbody>
            <td><?php echo $current_term['BaseAmt']; ?></td>
            <td><?php echo $contract_info['CAM']; ?></td>
        </tbody>
        <thead>
            <tr>
                <th scope="col">Due Date</th>
                <th scope="col">Late Date</th>
            </tr>
        </thead>
        <tbody>
            <td><?php echo $contract_info['DueDate']; ?></td>
            <td><?php echo $contract_info['LateDate']; ?></td>
        </tbody>
        <thead>
            <tr>
                <th scope="col">Late Fee</th>
                <th scope="col">Statement Send Date</th>
            </tr>
        </thead>
        <tbody>
            <td><?php echo $contract_info['LateFee']; ?></td>
            <td><?php echo $contract_info['StatementSendDate']; ?></td>
        </tbody>
    </table>
    <br><br>
    <h5>Evergreen Contract Information</h5>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Billing Cycle Start</th>
                <th scope="col">Billing Cycle End</th>
            </tr>
        </thead>
        <tbody>
            <td><?php echo $contract_info['BillingCycleStart']; ?></td>
            <td><?php echo $contract_info['BillingCycleEnd']; ?></td>
        </tbody>
    </table>
    <br><br>
    <h5>Fixed Contract Information</h5>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Outstanding Payments Due</th>
                <th scope="col">Total Payments Due</th>
            </tr>
        </thead>
        <tbody>
            <td><?php echo $contract_info['NumPaymentsDue']; ?></td>
            <td><?php echo $contract_info['TotalPaymentsDue']; ?></td>
        </tbody>
    </table>

    <div id="accordion">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Contract Terms
                    </button>
                </h5>
            </div>
            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Term Start Date</th>
                                <th scope="col">Term End Date</th>
                                <th scope="col">Base Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contract_terms as $contract_term) : ?>
                            <tr>
                                <td><?php echo $contract_term['TermStartDate']; ?></td>
                                <td><?php echo $contract_term['TermEndDate']; ?></td>
                                <td><?php echo $contract_term['BaseAmt']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- List of Modals -->
    <div class="modal fade" id="edit_statementautoreceive_modal" tabindex="-1" role="dialog" aria-labelledby="edit_statementautoreceive_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_statementautoreceive_modal_label">Statement Auto Receive</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_statementautoreceive">
                        <input type="hidden" name="contract_id" value="<?php echo $contract_info['ContractId']; ?>">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="new_statementautoreceive" id="new_statementautoreceive">
                            <label class="form-check-label" for="new_statementautoreceive">Statement Overdue Notifications</label>
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