<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Contact</h4>
        <a href="index.php?action=view_contact&contact_id=<?php echo $contact['ContactId']; ?>">Cancel</a>
    </div>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php">
        <input type="hidden" name="action" value="update_contact">
        <input type="hidden" name="ContactId" value="<?php echo $contact['ContactId']; ?>">

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="FirstName">First Name</label>
                <input type="text" class="form-control" id="FirstName" name="FirstName" value="<?php echo htmlspecialchars($contact['FirstName']); ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="LastName">Last Name</label>
                <input type="text" class="form-control" id="LastName" name="LastName" value="<?php echo htmlspecialchars($contact['LastName']); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="Address1">Address 1</label>
                <input type="text" class="form-control" id="Address1" name="Address1" value="<?php echo htmlspecialchars($contact['Address1']); ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="Address2">Address 2</label>
                <input type="text" class="form-control" id="Address2" name="Address2" value="<?php echo htmlspecialchars($contact['Address2']); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="City">City</label>
                <input type="text" class="form-control" id="City" name="City" value="<?php echo htmlspecialchars($contact['City']); ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="StateId">State</label>
                <select id="StateId" name="StateId" class="form-control">
                    <option value="">Select a state</option>
                    <?php foreach ($states as $state) : ?>
                        <option value="<?php echo $state['StateId']; ?>" <?php if ($state['StateId'] == $contact['StateId']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($state['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="Zip">Zip</label>
                <input type="text" class="form-control" id="Zip" name="Zip" value="<?php echo htmlspecialchars($contact['Zip']); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="Phone">Phone</label>
                <input type="text" class="form-control" id="Phone" name="Phone" value="<?php echo htmlspecialchars($contact['Phone']); ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="Email">Email</label>
                <input type="email" class="form-control" id="Email" name="Email" value="<?php echo htmlspecialchars($contact['Email']); ?>">
            </div>
        </div>

        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="ReceiveStatements" name="ReceiveStatements" value="1" <?php if (!empty($contact['ReceiveStatements'])) echo 'checked'; ?>>
            <label class="form-check-label" for="ReceiveStatements">Receive Statements</label>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="IsPrimary" name="IsPrimary" value="1" <?php if (!empty($contact['IsPrimary'])) echo 'checked'; ?>>
            <label class="form-check-label" for="IsPrimary">Is Primary</label>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</main>

<?php include '../view/footer.php'; ?>
