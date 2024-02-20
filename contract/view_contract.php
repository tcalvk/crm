<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<body onload="hide_elements()">
<script>
    var contract_type = <?php echo(json_encode($contract_info['ContractType'])); ?>;
    function hide_elements() {
        if (contract_type == 'Evergreen') {
            document.getElementById('accordion').style.visibility = 'visible';
        } else {
            document.getElementById('accordion').style.visibility = 'hidden';
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
        </thead>
        <tbody>
            <td><?php echo $contract_info['CompanyName']; ?></td>
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
</main>

<?php include '../view/footer.php'; ?>