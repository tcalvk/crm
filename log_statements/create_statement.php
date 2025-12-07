<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-muted mb-1">Customer</p>
            <h4 class="mb-0">Create Statement for <?php echo htmlspecialchars($customer_info['Name']); ?></h4>
        </div>
        <a href="index.php?action=view_all&customer_id=<?php echo $customer_info['CustomerId']; ?>">Back to Statements</a>
    </div>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="index.php">
                <input type="hidden" name="action" value="store_statement">
                <input type="hidden" name="CustomerId" value="<?php echo $customer_info['CustomerId']; ?>">

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="CreatedDate">Created Date</label>
                        <input type="date" class="form-control" id="CreatedDate" name="CreatedDate" value="<?php echo htmlspecialchars($statement_data['CreatedDate']); ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="DueDate">Due Date</label>
                        <input type="date" class="form-control" id="DueDate" name="DueDate" value="<?php echo htmlspecialchars($statement_data['DueDate']); ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="PaymentNumber">Payment # (optional)</label>
                        <input type="number" class="form-control" id="PaymentNumber" name="PaymentNumber" value="<?php echo htmlspecialchars($statement_data['PaymentNumber']); ?>" min="0" step="1">
                    </div>
                </div>

                <div class="form-group">
                    <label for="ContractId">Contract</label>
                    <select id="ContractId" name="ContractId" class="form-control" required>
                        <option value="">Select a contract</option>
                        <?php foreach ($contracts as $contract) : ?>
                            <option value="<?php echo $contract['ContractId']; ?>" <?php if ($contract['ContractId'] == $statement_data['ContractId']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($contract['Name'] ?? ('Contract #' . $contract['ContractId'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="BaseAmt">Base Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="BaseAmt" name="BaseAmt" value="<?php echo htmlspecialchars($statement_data['BaseAmt']); ?>" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="CAM">CAM</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="CAM" name="CAM" value="<?php echo htmlspecialchars($statement_data['CAM']); ?>" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create Statement</button>
            </form>
        </div>
    </div>
</main>

<?php include '../view/footer.php'; ?>
