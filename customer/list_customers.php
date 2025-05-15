<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <form method="post" action="index.php?action=delete_customer">
    <div class="d-flex justify-content-end mb-3">
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="actionsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Actions
            </button>
            <div class="dropdown-menu" aria-labelledby="actionsDropdown">
                <a class="dropdown-item" href="index.php?action=create_customer">Create Customer</a>
                <button class="dropdown-item" type="submit" name="delete_selected">Delete Customer</button>
            </div>
        </div>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col">Customer Name</th>
                <th scope="col">City</th>
                <th scope="col">State</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer) :
                if (empty($customer['Name'])) continue;
            ?>
            <tr>
                <td><input type="checkbox" name="selected_customers[]" value="<?php echo $customer['CustomerId']; ?>"></td>
                <td><a href="index.php?action=view_customer&customer_id=<?php echo $customer['CustomerId']; ?>"><?php echo $customer['Name']; ?></a></td>
                <td><?php echo $customer['City']; ?></td>
                <td><?php echo $customer['StateId']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </form>
<script>
document.querySelector("form[action='index.php?action=delete_customer']").addEventListener("submit", function(event) {
    const checkboxes = document.querySelectorAll("input[name='selected_customers[]']:checked");
    const count = checkboxes.length;
    if (count === 0) {
        alert("Please select at least one customer to delete.");
        event.preventDefault();
        return;
    }
    const confirmMessage = `Are you sure you want to delete ${count} customer(s)? This action cannot be undone, and you will lose all associated data.`;
    const userInput = prompt(confirmMessage + "\n\nTo confirm, type 'delete' below:");
    if (userInput !== "delete") {
        alert("Customer deletion canceled.");
        event.preventDefault();
    }
});
</script>
</main>

<?php include '../view/footer.php'; ?>