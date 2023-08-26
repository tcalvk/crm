<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Statement Number</th>
                <th scope="col">Created Date</th>
                <th scope="col">Total Amount</th>
                <th scope="col">Property Name</th>
                <th scope="col">Property Address 1</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($statements as $statement) : ?>
            <tr>
                <td><a href="../log_statements/index.php?action=view_statement&statement_number=<?php echo $statement['StatementNumber']; ?>"><?php echo $statement['StatementNumber']; ?></a>
                <td><?php echo $statement['CreatedDate']; ?> 
                <td>$<?php echo $statement['TotalAmt']; ?>
                <td><?php echo $statement['PropertyName']; ?></td>
                <td><?php echo $statement['Address1']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include '../view/footer.php'; ?>