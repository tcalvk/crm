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
                    <p><strong>Property:</strong>
                        <?php if (!empty($contract_info['PropertyId'])) : ?>
                            <a href="../property/index.php?action=view_property&property_id=<?php echo $contract_info['PropertyId']; ?>">
                                <?php echo htmlspecialchars($contract_info['PropertyName']); ?>
                            </a>
                        <?php else : ?>
                            <span class="text-muted">Not set</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Company:</strong>
                        <?php if (!empty($contract_info['CompanyId'])) : ?>
                            <a href="../company/index.php?action=view_company&company_id=<?php echo $contract_info['CompanyId']; ?>">
                                <?php echo htmlspecialchars($contract_info['CompanyName']); ?>
                            </a>
                        <?php else : ?>
                            <span class="text-muted">Not set</span>
                        <?php endif; ?>
                    </p>
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Contract Terms</span>
            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addTermModal">Add Term</button>
        </div>
        <div class="card-body">
            <?php if (!empty($term_errors)) : ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($term_errors as $error) : ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if (empty($contract_terms)) : ?>
                <p class="mb-0 text-muted">No terms recorded.</p>
            <?php else : ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Term Start Date</th>
                        <th scope="col">Term End Date</th>
                        <th scope="col">Base Amount</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contract_terms as $contract_term) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($contract_term['TermStartDate']); ?></td>
                        <td><?php echo htmlspecialchars($contract_term['TermEndDate']); ?></td>
                        <td><?php echo htmlspecialchars($contract_term['BaseAmt']); ?></td>
                        <td class="text-right">
                            <form method="post" action="index.php" class="d-inline" onsubmit="return confirm('Delete this contract term?');">
                                <input type="hidden" name="action" value="delete_contract_term">
                                <input type="hidden" name="contract_id" value="<?php echo $contract_info['ContractId']; ?>">
                                <input type="hidden" name="contract_term_id" value="<?php echo $contract_term['ContractTermId']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
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

<div class="modal fade" id="addTermModal" tabindex="-1" role="dialog" aria-labelledby="addTermModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="index.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTermModalLabel">Add Contract Term</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_contract_term">
                    <input type="hidden" name="contract_id" value="<?php echo $contract_info['ContractId']; ?>">
                    <?php if (!empty($term_errors)) : ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($term_errors as $error) : ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="termStartDate">Term Start Date</label>
                        <input type="date" class="form-control" id="termStartDate" name="TermStartDate" value="<?php echo htmlspecialchars($term_form_data['TermStartDate']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="termEndDate">Term End Date</label>
                        <input type="date" class="form-control" id="termEndDate" name="TermEndDate" value="<?php echo htmlspecialchars($term_form_data['TermEndDate']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="termBaseAmt">Base Amount</label>
                        <input type="number" step="0.01" class="form-control" id="termBaseAmt" name="BaseAmt" value="<?php echo htmlspecialchars($term_form_data['BaseAmt']); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Term</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

<?php if (!empty($term_errors)) : ?>
<script>
    $(function() {
        $('#addTermModal').modal('show');
    });
</script>
<?php endif; ?>

<?php include '../view/footer.php'; ?>
