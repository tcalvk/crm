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
            <p class="text-uppercase text-muted small mb-1">Customer</p>
            <h4 class="mb-1"><?php echo htmlspecialchars($customer_info['Name']); ?></h4>
            <div class="text-muted">
                <?php echo htmlspecialchars($customer_info['City']); ?>
                <?php echo $customer_info['StateId'] ? ', ' . htmlspecialchars($customer_info['StateId']) : ''; ?>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <a class="btn btn-primary btn-sm mr-3" href="index.php?action=edit_customer&customer_id=<?php echo $customer_id; ?>">Edit Customer</a>
            <a href="index.php?action=list_customers">Back to Customers</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Customer ID:</strong> <?php echo $customer_info['CustomerId']; ?></p>
                    <p><strong>Address 1:</strong> <?php echo htmlspecialchars($customer_info['Address1']); ?></p>
                    <p><strong>Address 2:</strong> <?php echo $customer_info['Address2'] ? htmlspecialchars($customer_info['Address2']) : '—'; ?></p>
                    <p><strong>Address 3:</strong> <?php echo $customer_info['Address3'] ? htmlspecialchars($customer_info['Address3']) : '—'; ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>City:</strong> <?php echo htmlspecialchars($customer_info['City']); ?></p>
                    <p><strong>State:</strong> <?php echo htmlspecialchars($customer_info['StateId']); ?></p>
                    <p><strong>Zip:</strong> <?php echo htmlspecialchars($customer_info['Zip']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion" id="customerAccordion">
        <div class="card">
            <div class="card-header" id="headingContracts">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseContracts" aria-expanded="false" aria-controls="collapseContracts">
                        Contracts
                    </button>
                </h5>
            </div>
            <div id="collapseContracts" class="collapse" aria-labelledby="headingContracts" data-parent="#customerAccordion">
                <div class="card-body">
                    <?php if (empty($contracts)) : ?>
                        <p class="mb-3 text-muted">No contracts on file yet.</p>
                        <a class="btn btn-primary btn-sm" href="../contract/index.php?action=create_contract&customer_id=<?php echo $customer_id; ?>">Create Contract</a>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Due Date</th>
                                        <th scope="col">Auto Receive</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contracts as $contract) : ?>
                                    <tr>
                                        <td><a href="../contract/index.php?action=view_contract&contract_id=<?php echo $contract['ContractId']; ?>"><?php echo htmlspecialchars($contract['Name']); ?></a></td>
                                        <td><?php echo htmlspecialchars($contract['ContractType']); ?></td>
                                        <td><?php echo htmlspecialchars($contract['DueDate']); ?></td>
                                        <td><?php echo ($contract['StatementAutoReceive'] === 'true') ? 'Yes' : 'No'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-center mt-3">
                        <a class="btn btn-link" href="../contract/index.php?action=view_contracts_list&customer_id=<?php echo $customer_id; ?>">View All Contracts</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingStatements">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseStatements" aria-expanded="false" aria-controls="collapseStatements">
                        Statements
                    </button>
                </h5>
            </div>
            <div id="collapseStatements" class="collapse" aria-labelledby="headingStatements" data-parent="#customerAccordion">
                <div class="card-body">
                    <?php if (empty($statements)) : ?>
                        <p class="mb-0 text-muted">No statements found.</p>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Statement Number</th>
                                        <th scope="col">Created Date</th>
                                        <th scope="col">Total Amount</th>
                                        <th scope="col">Property Name</th>
                                        <th scope="col">Property Address 1</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($statements as $statement) : ?>
                                    <tr>
                                        <td><a href="../log_statements/index.php?action=view_statement&statement_number=<?php echo $statement['StatementNumber']; ?>"><?php echo htmlspecialchars($statement['StatementNumber']); ?></a></td>
                                        <td><?php echo htmlspecialchars($statement['CreatedDate']); ?></td>
                                        <td>$<?php echo htmlspecialchars($statement['TotalAmt']); ?></td>
                                        <td><?php echo htmlspecialchars($statement['PropertyName']); ?></td>
                                        <td><?php echo htmlspecialchars($statement['Address1']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-center mt-3">
                        <a href="../log_statements/index.php?action=view_all&customer_id=<?php echo $customer_id; ?>">View All</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingPayments">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapsePayments" aria-expanded="false" aria-controls="collapsePayments">
                        Payment Methods
                    </button>
                </h5>
            </div>
            <div id="collapsePayments" class="collapse" aria-labelledby="headingPayments" data-parent="#customerAccordion">
                <div class="card-body">
                    <?php if (empty($payment_methods)) : ?>
                        <p class="mb-0 text-muted">No payment methods on file yet.</p>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Bank Name</th>
                                        <th scope="col">Last 4</th>
                                        <th scope="col">Account Type</th>
                                        <th scope="col">Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($payment_methods, 0, 3) as $payment_method) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($payment_method['BankName']); ?></td>
                                        <td><?php echo htmlspecialchars($payment_method['Last4']); ?></td>
                                        <td><?php echo htmlspecialchars($payment_method['AccountType']); ?></td>
                                        <td><?php echo htmlspecialchars($payment_method['CreatedAt']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-center mt-3">
                        <a href="../payment_methods/index.php?action=list_payment_methods&customer_id=<?php echo $customer_id; ?>">View All</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingContacts">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseContacts" aria-expanded="false" aria-controls="collapseContacts">
                        Contacts
                    </button>
                </h5>
            </div>
            <div id="collapseContacts" class="collapse" aria-labelledby="headingContacts" data-parent="#customerAccordion">
                <div class="card-body">
                    <?php if (empty($contacts)) : ?>
                        <p class="mb-3 text-muted">No contacts on file yet.</p>
                        <a class="btn btn-primary btn-sm" href="../contact/index.php?action=create_contact&customer_id=<?php echo $customer_id; ?>">Create Contact</a>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">First Name</th>
                                        <th scope="col">Last Name</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Receive Statements</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contacts as $contact) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($contact['FirstName']); ?></td>
                                        <td><?php echo htmlspecialchars($contact['LastName']); ?></td>
                                        <td><?php echo htmlspecialchars($contact['Phone']); ?></td>
                                        <td><?php echo htmlspecialchars($contact['Email']); ?></td>
                                        <td><?php echo !empty($contact['ReceiveStatements']) ? 'Yes' : 'No'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-center mt-3">
                        <a class="btn btn-link" href="../contact/index.php?action=customer_contacts&customer_id=<?php echo $customer_id; ?>">View All Contacts</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../view/footer.php'; ?>

<?php include '../view/footer.php'; ?>
