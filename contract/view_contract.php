<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 d-inline"><?php echo htmlspecialchars($contract_info['Name']); ?></h4>
            <span class="badge badge-secondary ml-2"><?php echo htmlspecialchars($contract_info['ContractType']); ?></span>
        </div>
        <div class="d-flex align-items-center">
            <a class="btn btn-primary btn-sm mr-2" href=".?action=edit_contract&contract_id=<?php echo $contract_info['ContractId']; ?>">Edit Contract</a>
            <button class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#confirmDelete">Delete</button>
            <a href=".?action=view_contracts_list&customer_id=<?php echo $contract_info['CustomerId']; ?>">Back to Contracts</a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Contract ID:</strong> <?php echo $contract_info['ContractId']; ?></p>
                    <p><strong>Customer:</strong> <a href="../customer/index.php?action=view_customer&customer_id=<?php echo $contract_info['CustomerId']; ?>"><?php echo htmlspecialchars($contract_info['CustomerName']); ?></a></p>
                    <p><strong>Property ID:</strong> <?php echo htmlspecialchars($contract_info['PropertyId']); ?></p>
                    <p><strong>Company ID:</strong> <?php echo htmlspecialchars($contract_info['CompanyId']); ?></p>
                    <p><strong>Base Amount:</strong> <?php echo htmlspecialchars($contract_info['BaseAmt']); ?></p>
                    <p><strong>CAM:</strong> <?php echo htmlspecialchars($contract_info['CAM']); ?></p>
                    <p><strong>Statement Auto Receive:</strong> <?php echo ($contract_info['StatementAutoReceive'] === 'true') ? 'Yes' : 'No'; ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Billing Cycle Start:</strong> <?php echo htmlspecialchars($contract_info['BillingCycleStart']); ?></p>
                    <p><strong>Billing Cycle End:</strong> <?php echo htmlspecialchars($contract_info['BillingCycleEnd']); ?></p>
                    <p><strong>Due Date:</strong> <?php echo htmlspecialchars($contract_info['DueDate']); ?></p>
                    <p><strong>Late Date:</strong> <?php echo htmlspecialchars($contract_info['LateDate']); ?></p>
                    <p><strong>Late Fee:</strong> <?php echo htmlspecialchars($contract_info['LateFee']); ?></p>
                    <p><strong>Statement Send Date:</strong> <?php echo htmlspecialchars($contract_info['StatementSendDate']); ?></p>
                    <p><strong>Test Contract:</strong> <?php echo !empty($contract_info['TestContract']) ? 'Yes' : 'No'; ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Num Payments Due:</strong> <?php echo htmlspecialchars($contract_info['NumPaymentsDue']); ?></p>
                    <p><strong>Total Payments Due:</strong> <?php echo htmlspecialchars($contract_info['TotalPaymentsDue']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Stripe Payment Method Id:</strong> <?php echo htmlspecialchars($contract_info['StripePaymentMethodId']); ?></p>
                    <?php if (!empty($contract_info['BankName'])) : ?>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($contract_info['BankName'] . ' ••••' . $contract_info['Last4']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($current_term)) : ?>
    <div class="card mb-3">
        <div class="card-header">Current Term</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Term Start:</strong> <?php echo htmlspecialchars($current_term['TermStartDate']); ?></div>
                <div class="col-md-4"><strong>Term End:</strong> <?php echo htmlspecialchars($current_term['TermEndDate']); ?></div>
                <div class="col-md-4"><strong>Base Amount:</strong> <?php echo htmlspecialchars($current_term['BaseAmt']); ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">Contract Terms</div>
        <div class="card-body">
            <?php if (empty($contract_terms)) : ?>
                <p class="mb-0 text-muted">No terms recorded.</p>
            <?php else : ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Term Start Date</th>
                        <th scope="col">Term End Date</th>
                        <th scope="col">Base Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contract_terms as $contract_term) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contract_term['TermStartDate']); ?></td>
                        <td><?php echo htmlspecialchars($contract_term['TermEndDate']); ?></td>
                        <td><?php echo htmlspecialchars($contract_term['BaseAmt']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</main>

<form id="deleteContractForm" method="post" action="index.php">
    <input type="hidden" name="action" value="delete_contract">
    <input type="hidden" name="contract_id" value="<?php echo $contract_info['ContractId']; ?>">
</form>

<div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">Delete Contract</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <?php echo htmlspecialchars($contract_info['Name']); ?>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteContractForm').submit();">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php include '../view/footer.php'; ?>
