<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Contract</h4>
        <a href=".?action=view_contract&contract_id=<?php echo $contract['ContractId']; ?>">Cancel</a>
    </div>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php">
        <input type="hidden" name="action" value="update_contract">
        <input type="hidden" name="ContractId" value="<?php echo $contract['ContractId']; ?>">
        <input type="hidden" name="CustomerId" value="<?php echo $contract['CustomerId']; ?>">

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="Name">Name</label>
                <input type="text" class="form-control" id="Name" name="Name" value="<?php echo htmlspecialchars($contract['Name']); ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="ContractType">Contract Type</label>
                <select id="ContractType" name="ContractType" class="form-control" required>
                    <option value="">Select type</option>
                    <?php 
                        $types = ['Evergreen', 'Fixed'];
                        foreach ($types as $type) : 
                    ?>
                        <option value="<?php echo $type; ?>" <?php if ($contract['ContractType'] === $type) echo 'selected'; ?>>
                            <?php echo $type; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="PropertyId">Property Id</label>
                <input type="number" class="form-control" id="PropertyId" name="PropertyId" value="<?php echo htmlspecialchars($contract['PropertyId']); ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="CompanyId">Company Id</label>
                <input type="number" class="form-control" id="CompanyId" name="CompanyId" value="<?php echo htmlspecialchars($contract['CompanyId']); ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="StripePaymentMethodId">Payment Method Id</label>
                <select class="form-control" id="StripePaymentMethodId" name="StripePaymentMethodId">
                    <option value="0" <?php echo empty($contract['StripePaymentMethodId']) ? 'selected' : ''; ?>>None</option>
                    <?php foreach ($customer_payment_methods as $method) : ?>
                        <option value="<?php echo $method['StripePaymentMethodId']; ?>" <?php if ($contract['StripePaymentMethodId'] == $method['StripePaymentMethodId']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($method['AccountType'] . ' - ' . $method['BankName'] . ' ••••' . $method['Last4']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="BaseAmt">Base Amount</label>
                <input type="text" class="form-control" id="BaseAmt" name="BaseAmt" value="<?php echo htmlspecialchars($contract['BaseAmt']); ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="CAM">CAM</label>
                <input type="text" class="form-control" id="CAM" name="CAM" value="<?php echo htmlspecialchars($contract['CAM']); ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="LateFee">Late Fee</label>
                <input type="text" class="form-control" id="LateFee" name="LateFee" value="<?php echo htmlspecialchars($contract['LateFee']); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="BillingCycleStart">Billing Cycle Start</label>
                <input type="number" class="form-control" id="BillingCycleStart" name="BillingCycleStart" value="<?php echo htmlspecialchars($contract['BillingCycleStart']); ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="BillingCycleEnd">Billing Cycle End</label>
                <input type="text" class="form-control" id="BillingCycleEnd" name="BillingCycleEnd" value="<?php echo htmlspecialchars($contract['BillingCycleEnd']); ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="DueDate">Due Date (day of month)</label>
                <input type="text" class="form-control" id="DueDate" name="DueDate" value="<?php echo htmlspecialchars($contract['DueDate']); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="LateDate">Late Date</label>
                <input type="number" class="form-control" id="LateDate" name="LateDate" value="<?php echo htmlspecialchars($contract['LateDate']); ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="StatementSendDate">Statement Send Date</label>
                <input type="number" class="form-control" id="StatementSendDate" name="StatementSendDate" value="<?php echo htmlspecialchars($contract['StatementSendDate']); ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="NumPaymentsDue">Outstanding Payments Due</label>
                <input type="number" class="form-control" id="NumPaymentsDue" name="NumPaymentsDue" value="<?php echo htmlspecialchars($contract['NumPaymentsDue']); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="TotalPaymentsDue">Total Payments Due</label>
                <input type="number" class="form-control" id="TotalPaymentsDue" name="TotalPaymentsDue" value="<?php echo htmlspecialchars($contract['TotalPaymentsDue']); ?>">
            </div>
            <div class="form-group col-md-4">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" id="StatementAutoReceive" name="StatementAutoReceive" value="1" <?php if ($contract['StatementAutoReceive'] === 'true') echo 'checked'; ?>>
                    <label class="form-check-label" for="StatementAutoReceive">Statement Auto Receive</label>
                </div>
            </div>
            <div class="form-group col-md-4">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" id="TestContract" name="TestContract" value="1" <?php if (!empty($contract['TestContract'])) echo 'checked'; ?>>
                    <label class="form-check-label" for="TestContract">Test Contract</label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</main>

<?php include '../view/footer.php'; ?>
