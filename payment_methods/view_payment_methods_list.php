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
            </tr>
        </thead>
        <tbody>
        <?php foreach ($payment_methods as $payment_method) : ?>
            <tr>
                <td><?php echo $payment_method['BankName']; ?></td>
                <td><?php echo $payment_method['Last4']; ?></td>
                <td><?php echo $payment_method['AccountType']; ?></td>
                <td><?php echo $payment_method['CreatedAt']; ?></td>
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
})();
</script>

<?php include '../view/footer.php'; ?>
