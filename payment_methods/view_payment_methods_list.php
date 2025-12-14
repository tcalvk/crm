<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Payment Methods for <?php echo $customer_info['Name']; ?></h4>
        <div class="d-flex align-items-center">
            <button id="sendInviteBtn" class="btn btn-primary btn-sm mr-2" type="button">Send payment method request</button>
            <a href="../customer/index.php?action=view_customer&customer_id=<?php echo $customer_id; ?>">Back to Customer</a>
        </div>
    </div>

    <?php if (empty($payment_methods)) : ?>
        <div class="alert alert-info">No payment methods on file for this customer.</div>
    <?php else : ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Bank Name</th>
                <th scope="col">Last 4</th>
                <th scope="col">Account Type</th>
                <th scope="col">Created</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($payment_methods as $payment_method) : ?>
            <tr>
                <td>
                    <div><?php echo $payment_method['BankName']; ?></div>
                    <div class="small">
                        <?php if (!empty($payment_method['IsEnabled'])) : ?>
                            <span class="badge badge-success">Enabled</span>
                        <?php else : ?>
                            <span class="badge badge-warning text-dark">Disabled</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td><?php echo $payment_method['Last4']; ?></td>
                <td><?php echo $payment_method['AccountType']; ?></td>
                <td><?php echo $payment_method['CreatedAt']; ?></td>
                <td>
                    <form method="post" action="index.php" class="d-inline">
                        <input type="hidden" name="action" value="toggle_payment_method">
                        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
                        <input type="hidden" name="payment_method_id" value="<?php echo $payment_method['StripePaymentMethodId']; ?>">
                        <input type="hidden" name="enable" value="<?php echo $payment_method['IsEnabled'] ? 0 : 1; ?>">
                        <button type="submit" class="btn btn-sm <?php echo $payment_method['IsEnabled'] ? 'btn-warning' : 'btn-success'; ?>">
                            <?php echo $payment_method['IsEnabled'] ? 'Disable' : 'Enable'; ?>
                        </button>
                    </form>
                    <form method="post" action="index.php" class="d-inline delete-form" data-bank="<?php echo htmlspecialchars($payment_method['BankName']); ?>" data-last4="<?php echo htmlspecialchars($payment_method['Last4']); ?>">
                        <input type="hidden" name="action" value="delete_payment_method">
                        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
                        <input type="hidden" name="payment_method_id" value="<?php echo $payment_method['StripePaymentMethodId']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger ml-1">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <div id="inviteStatus" class="mt-3"></div>

    <div class="modal fade" id="confirmInviteModal" tabindex="-1" role="dialog" aria-labelledby="confirmInviteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmInviteModalLabel">Send payment method request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Request will be sent to primary customer contact. Are you sure you'd like to send?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSendInviteBtn">Yes</button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
(function() {
    const sendBtn = document.getElementById('sendInviteBtn');
    const confirmBtn = document.getElementById('confirmSendInviteBtn');
    const statusEl = document.getElementById('inviteStatus');
    const deleteForms = document.querySelectorAll('.delete-form');

    function setStatus(message, isError = false) {
        statusEl.textContent = message;
        statusEl.className = isError ? 'text-danger' : 'text-success';
    }

    async function sendInvite() {
        setStatus('Sending request...');
        sendBtn.disabled = true;
        confirmBtn.disabled = true;
        try {
            const formData = new FormData();
            formData.append('customer_id', '<?php echo $customer_id; ?>');
            const response = await fetch('../create_payment_method_invite.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (err) {
                console.error('Raw response (non-JSON):', text);
                throw new Error('Unexpected response: ' + text);
            }

            if (!response.ok || data.success === false) {
                throw new Error(data.error || 'Unable to send invite');
            }

            if (data.inviteCode) {
                setStatus('Invite created. Code: ' + data.inviteCode);
                alert('Invite code for customer: ' + data.inviteCode + '\\n\\nCode sent to the customer with the billing link.');
            } else {
                setStatus('Invite created.');
            }
        } catch (err) {
            console.error(err);
            setStatus(err.message || 'Failed to send invite', true);
            alert(err.message || 'Failed to send invite');
        } finally {
            sendBtn.disabled = false;
            confirmBtn.disabled = false;
        }
    }

    sendBtn.addEventListener('click', () => {
        if (sendBtn.disabled) return;
        $('#confirmInviteModal').modal('show');
    });

    confirmBtn.addEventListener('click', () => {
        $('#confirmInviteModal').modal('hide');
        sendInvite();
    });

    deleteForms.forEach((form) => {
        form.addEventListener('submit', (e) => {
            const bank = form.dataset.bank || 'payment method';
            const last4 = form.dataset.last4 ? ` ending in ${form.dataset.last4}` : '';
            const confirmation = prompt(`Type delete to remove ${bank}${last4}. This cannot be undone.`);
            if (!confirmation || confirmation.toLowerCase() !== 'delete') {
                e.preventDefault();
                return false;
            }
            return true;
        });
    });
})();
</script>

<?php include '../view/footer.php'; ?>
