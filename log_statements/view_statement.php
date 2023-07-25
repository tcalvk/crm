<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <br>
    <h3>Statement: <?php echo $statement['StatementNumber']; ?></h3>
    <h4><a href=""><?php echo $statement['CustomerName']; ?></a></h4>
    <br>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Created Date</th>
                <th scope="col">Paid Date</th>
                <th scope="col">Total Amount</th>
                <th scope="col">Payment Number</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $statement['CreatedDate']; ?>
                <td><?php echo $statement['PaidDate']; ?> 
                <td>$<?php echo $statement['TotalAmt']; ?>
                <td><?php echo $statement['PaymentNumber']; ?>
            </tr>
        </tbody>
    </table>
</main>

<?php include '../view/footer.php'; ?>