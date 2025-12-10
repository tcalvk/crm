<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Create Property</h4>
        <a href=".?action=list_properties">Back to Properties</a>
    </div>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error) : ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="index.php" method="post">
        <input type="hidden" name="action" value="store_property">
        <div class="card">
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="Name">Name</label>
                        <input type="text" class="form-control" id="Name" name="Name" value="<?php echo htmlspecialchars($property['Name']); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="Address1">Address 1</label>
                        <input type="text" class="form-control" id="Address1" name="Address1" value="<?php echo htmlspecialchars($property['Address1']); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="Address2">Address 2</label>
                        <input type="text" class="form-control" id="Address2" name="Address2" value="<?php echo htmlspecialchars($property['Address2']); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="Address3">Address 3</label>
                        <input type="text" class="form-control" id="Address3" name="Address3" value="<?php echo htmlspecialchars($property['Address3']); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="City">City</label>
                        <input type="text" class="form-control" id="City" name="City" value="<?php echo htmlspecialchars($property['City']); ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="StateId">State</label>
                        <select class="form-control" id="StateId" name="StateId" required>
                            <option value="">Select State</option>
                            <?php foreach ($states as $state) : ?>
                                <option value="<?php echo htmlspecialchars($state['StateId']); ?>" <?php echo ($property['StateId'] === $state['StateId']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($state['StateId'] . ' - ' . $state['Name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="Zip">Zip</label>
                        <input type="text" class="form-control" id="Zip" name="Zip" value="<?php echo htmlspecialchars($property['Zip']); ?>" required>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Create Property</button>
            </div>
        </div>
    </form>
</main>

<?php include '../view/footer.php'; ?>
