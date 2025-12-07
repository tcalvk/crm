<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';

$is_written_off = !empty($statement['WrittenOff']);
$can_mark_paid = $display && $display_markpaid;
$status = $statement['Status'];
?>

<main class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-muted mb-1">
                Customer: <a href="../customer/index.php?action=view_customer&customer_id=<?php echo $customer_info['CustomerId']; ?>"><?php echo htmlspecialchars($customer_info['Name']); ?></a>
            </p>
            <div class="d-flex align-items-center">
                <h4 class="mb-0 mr-3">Statement #<?php echo htmlspecialchars($statement['StatementNumber']); ?></h4>
                <span class="badge <?php echo $status === 'Paid' ? 'badge-success' : ($status === 'Partial Payment' ? 'badge-info' : 'badge-warning'); ?>">
                    <?php echo htmlspecialchars($status); ?>
                </span>
            </div>
            <?php if (!empty($statement['Property'])) : ?>
                <small class="text-muted d-block mt-1">Property: <?php echo htmlspecialchars($statement['Property']); ?></small>
            <?php endif; ?>
        </div>
        <div class="d-flex align-items-center">
            <a class="btn btn-link mr-2" href="index.php?action=view_all&customer_id=<?php echo $customer_info['CustomerId']; ?>">Back to Statements</a>
            <?php if ($can_mark_paid) : ?>
                <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#markPaidModal">Mark as Paid</button>
            <?php else : ?>
                <button class="btn btn-primary btn-sm mr-2" disabled>Mark as Paid</button>
            <?php endif; ?>
            <?php if (!empty($statement['PaidDate']) && !$is_written_off) : ?>
                <form action="index.php" method="post" class="mb-0 mr-2">
                    <input type="hidden" name="action" value="clear_paid_date">
                    <input type="hidden" name="statement_number" value="<?php echo $statement['StatementNumber']; ?>">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Clear Paid Date</button>
                </form>
            <?php endif; ?>
            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteStatementModal">Delete</button>
        </div>
    </div>

    <?php if ($is_written_off) : ?>
        <div class="alert alert-danger">This statement has been written off.</div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Statement Details</h5>
                        <span class="text-muted small">Auto receive: <?php echo htmlspecialchars($statement_auto_receive); ?></span>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Created:</strong> <?php echo htmlspecialchars($statement['CreatedDate']); ?></p>
                            <p class="mb-1"><strong>Due Date:</strong> <?php echo htmlspecialchars($statement['DueDate']); ?></p>
                            <p class="mb-1"><strong>Payment #:</strong> <?php echo htmlspecialchars($statement['PaymentNumber']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Base Amount:</strong> $<?php echo number_format((float) $statement['BaseAmt'], 2); ?></p>
                            <p class="mb-1"><strong>CAM:</strong> $<?php echo number_format((float) $statement['CAM'], 2); ?></p>
                            <p class="mb-1"><strong>Total:</strong> $<?php echo number_format((float) $statement['TotalAmt'], 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Payment</h5>
                        <?php if (!empty($statement['PaidDate'])) : ?>
                            <span class="badge badge-success">Paid</span>
                        <?php else : ?>
                            <span class="badge badge-warning">Unpaid</span>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Paid Date:</strong> <?php echo htmlspecialchars($statement['PaidDate']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Payment Amount:</strong> $<?php echo number_format((float) $statement['PaymentAmount'], 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-2">Automation</h5>
                    <p class="mb-0 text-muted"><?php echo htmlspecialchars($statement_auto_receive); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-2">Related Records</h5>
                    <p class="mb-1"><strong>Contract:</strong> <a href="../contract/index.php?action=view_contract&contract_id=<?php echo $statement['ContractId']; ?>"><?php echo htmlspecialchars($statement['ContractName']); ?></a></p>
                    <p class="mb-1"><strong>Customer:</strong> <a href="../customer/index.php?action=view_customer&customer_id=<?php echo $statement['CustomerId']; ?>"><?php echo htmlspecialchars($statement['CustomerName']); ?></a></p>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="markPaidModal" tabindex="-1" role="dialog" aria-labelledby="markPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markPaidModalLabel">Mark Statement as Paid</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="index.php" method="post">
                    <input type="hidden" name="action" value="mark_paid">
                    <input type="hidden" name="statement_number" value="<?php echo $statement['StatementNumber']; ?>">
                    <div class="form-group">
                        <label for="paid_date" class="col-form-label">Date Paid:</label>
                        <input type="date" class="form-control" id="paid_date" name="paid_date">
                    </div>
                    <div class="form-group">
                        <label for="payment_amount" class="col-form-label">Payment Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" value="<?php echo htmlspecialchars($statement['PaymentAmount']); ?>" min="0" step="0.01" name="payment_amount" id="payment_amount" class="form-control">
                        </div>
                    </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Save">
                </div>
                </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteStatementModal" tabindex="-1" role="dialog" aria-labelledby="deleteStatementModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteStatementModalLabel">Delete Statement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Deleting this statement cannot be undone. Continue?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="index.php" method="post" class="mb-0">
                    <input type="hidden" name="action" value="delete_statement">
                    <input type="hidden" name="statement_number" value="<?php echo $statement['StatementNumber']; ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../view/footer.php'; ?>
