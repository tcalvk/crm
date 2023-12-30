<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

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
                <th scope="col">Base Amount</th>
                <th scope="col">CAM</th>
            </tr>
        </thead>
        <tbody>
            <td><?php echo $contract_info['BaseAmt']; ?></td>
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
                <th scope="col">Number of Payments Due</th>
                <th scope="col">Total Payments Due</th>
            </tr>
        </thead>
        <tbody>
            <td><?php echo $contract_info['NumPaymentsDue']; ?></td>
            <td><?php echo $contract_info['TotalPaymentsDue']; ?></td>
        </tbody>
    </table>
</main>

<?php include '../view/footer.php'; ?>