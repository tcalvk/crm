<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><?php echo htmlspecialchars($contact['FirstName'] . ' ' . $contact['LastName']); ?></h4>
        <div class="d-flex align-items-center">
            <a class="btn btn-primary btn-sm mr-2" href="index.php?action=edit_contact&contact_id=<?php echo $contact['ContactId']; ?>">Edit Contact</a>
            <button type="button" class="btn btn-danger btn-sm mr-2" data-toggle="modal" data-target="#confirmDelete">Delete</button>
            <a href="index.php?action=customer_contacts&customer_id=<?php echo $contact['CustomerId']; ?>">Back to Contacts</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Contact ID:</strong> <?php echo $contact['ContactId']; ?></p>
                    <p><strong>Customer ID:</strong> <?php echo $contact['CustomerId']; ?></p>
                    <p><strong>First Name:</strong> <?php echo htmlspecialchars($contact['FirstName']); ?></p>
                    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($contact['LastName']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($contact['Phone']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($contact['Email']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Address 1:</strong> <?php echo htmlspecialchars($contact['Address1']); ?></p>
                    <p><strong>Address 2:</strong> <?php echo htmlspecialchars($contact['Address2']); ?></p>
                    <p><strong>City:</strong> <?php echo htmlspecialchars($contact['City']); ?></p>
                    <p><strong>State:</strong> <?php echo htmlspecialchars($contact['StateId']); ?></p>
                    <p><strong>Zip:</strong> <?php echo htmlspecialchars($contact['Zip']); ?></p>
                    <p><strong>Receive Statements:</strong>
                        <?php echo !empty($contact['ReceiveStatements']) ? 'Yes' : 'No'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<form id="deleteContactForm" method="post" action="index.php">
    <input type="hidden" name="action" value="delete_contact">
    <input type="hidden" name="contact_id" value="<?php echo $contact['ContactId']; ?>">
</form>

<div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">Delete Contact</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <?php echo htmlspecialchars($contact['FirstName'] . ' ' . $contact['LastName']); ?>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteContactForm').submit();">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php include '../view/footer.php'; ?>
