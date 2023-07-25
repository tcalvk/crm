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
                <th scope="col">Customer Name</th>
                <th scope="col">City</th>
                <th scope="col">State</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer) : ?>
            <tr>
                <td><a href="index.php?action=view_customer&customer_id=<?php echo $customer['CustomerId']; ?>"><?php echo $customer['Name']; ?></a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include '../view/footer.php'; ?>