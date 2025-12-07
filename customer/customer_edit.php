<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Customer</h4>
        <a href="index.php?action=view_customer&customer_id=<?php echo $customer['CustomerId']; ?>">Cancel</a>
    </div>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php">
        <input type="hidden" name="action" value="update_customer">
        <input type="hidden" name="CustomerId" value="<?php echo $customer['CustomerId']; ?>">

        <div class="form-group">
            <label for="Name">Customer Name</label>
            <input type="text" class="form-control" id="Name" name="Name" value="<?php echo htmlspecialchars($customer['Name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="Address1">Address 1</label>
            <input type="text" class="form-control" id="Address1" name="Address1" value="<?php echo htmlspecialchars($customer['Address1']); ?>" required>
        </div>

        <div class="form-group">
            <label for="Address2">Address 2</label>
            <input type="text" class="form-control" id="Address2" name="Address2" value="<?php echo htmlspecialchars($customer['Address2']); ?>">
        </div>

        <div class="form-group">
            <label for="Address3">Address 3</label>
            <input type="text" class="form-control" id="Address3" name="Address3" value="<?php echo htmlspecialchars($customer['Address3']); ?>">
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="City">City</label>
                <input type="text" class="form-control" id="City" name="City" value="<?php echo htmlspecialchars($customer['City']); ?>" required>
            </div>
            <div class="form-group col-md-4">
                <label for="StateId">State</label>
                <select id="StateId" name="StateId" class="form-control" required>
                    <option value="">Select a state</option>
                    <?php foreach ($states as $state) : ?>
                        <option value="<?php echo $state['StateId']; ?>" <?php if ($state['StateId'] == $customer['StateId']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($state['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="Zip">Zip</label>
                <input type="text" class="form-control" id="Zip" name="Zip" value="<?php echo htmlspecialchars($customer['Zip']); ?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</main>

<?php include '../view/footer.php'; ?>
