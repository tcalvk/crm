<?php
session_start();
$configPath = __DIR__ . '/config/stripe_config.php';
if (!file_exists($configPath)) {
    $configPath = __DIR__ . '/config/stripe_dev.php';
}
$stripeConfig = require $configPath;
$stripePublishableKey = $stripeConfig['stripe_publishable_key'] ?? 'pk_test_YOUR_PUBLISHABLE_KEY_HERE';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bank Account</title>
    <script src="https://js.stripe.com/v3/"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        :root {
            --accent: #0d6efd;
            --muted: #6c757d;
        }
        body {
            background: linear-gradient(135deg, #f8fbff 0%, #eef2f7 100%);
            font-family: "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
        }
        .page-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 32px 14px;
        }
        .card-shell {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        }
        .pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: #e7f0ff;
            color: #0b5ed7;
            font-weight: 600;
            letter-spacing: 0.01em;
            font-size: 0.85rem;
        }
        .label-strong {
            font-weight: 600;
            color: #1f2d3d;
        }
        .card-subtle {
            background: #f8f9fb;
            border: 1px solid #e7ecf3;
            border-radius: 12px;
        }
        .callout {
            border: 1px dashed #c9d4e4;
            border-radius: 12px;
            background: #fdfefe;
            color: #4c5968;
        }
        .hidden {
            display: none;
        }
        .icon-circle {
            height: 48px;
            width: 48px;
            border-radius: 50%;
            background: #0d6efd;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            letter-spacing: 0.04em;
        }
        .hint {
            color: var(--muted);
            font-size: 0.9rem;
        }
        .btn-md-auto {
            width: 100%;
        }
        @media (min-width: 768px) {
            .btn-md-auto {
                width: auto;
            }
        }
        @media (max-width: 575.98px) {
            .input-group-lg > .form-control {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrap">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-7 col-lg-8">
                    <div class="card card-shell">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-start mb-4">
                                <div class="icon-circle mr-3">ACH</div>
                                <div>
                                    <div class="text-uppercase text-muted small mb-1">Billing</div>
                                    <h3 class="mb-1">Add Bank Account</h3>
                                    <p class="text-secondary mb-0">Securely connect your bank account in a couple of steps.</p>
                                </div>
                            </div>

                            <div class="callout p-3 mb-4">
                                <div class="d-flex align-items-center">
                                    <span class="pill mr-2">Stripe Secure Flow</span>
                                    <span class="text-muted small">We never see your login details.</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="pill mr-2">Step 1</span>
                                    <span class="text-muted">Verify your payment setup code</span>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="label-strong" for="codeInput">Payment setup code</label>
                                    <div class="input-group input-group-lg">
                                        <input id="codeInput" type="text" class="form-control" placeholder="e.g. ABCD-1234">
                                        <div class="input-group-append">
                                            <button id="verifyCodeBtn" type="button" class="btn btn-primary">Continue</button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Use the code sent to you by your account manager.</small>
                                </div>
                            </div>

                            <div id="connectContainer" class="hidden">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="pill mr-2">Step 2</span>
                                    <span class="text-muted">Share account holder details</span>
                                </div>
                                <form id="payment-method-form" novalidate class="card card-subtle p-3 mb-3">
                                    <div class="form-row">
                                        <div class="form-group col-12 col-md-6">
                                            <label class="label-strong" for="account-holder-name-field">Account holder name</label>
                                            <input id="account-holder-name-field" type="text" class="form-control form-control-lg" placeholder="Full name" required>
                                        </div>
                                        <div class="form-group col-12 col-md-6">
                                            <label class="label-strong" for="email-field">Email</label>
                                            <input id="email-field" type="email" class="form-control form-control-lg" placeholder="you@example.com" required>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-md-row align-items-md-center">
                                        <button id="connectBankBtn" type="submit" class="btn btn-primary btn-lg btn-block btn-md-auto px-4" disabled>Connect bank account</button>
                                        <span class="hint mt-2 mt-md-0 ml-md-3">You will see a secure Stripe window to verify your bank.</span>
                                    </div>
                                </form>

                                <form id="confirmation-form" class="hidden card card-subtle p-3">
                                    <div class="label-strong mb-2">Confirmation</div>
                                    <div id="confirmation-details" class="mb-2 text-dark"></div>
                                    <p class="text-muted mb-3" style="max-width: 720px;">
                                        By confirming, you authorize debits from this bank account according to the mandate shown in Stripe&apos;s secure dialog.
                                    </p>
                                    <button type="submit" class="btn btn-success btn-lg">Confirm bank account</button>
                                </form>
                            </div>

                            <div id="status" class="alert d-none mt-3" role="alert"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const statusEl = document.getElementById('status');
        const verifyBtn = document.getElementById('verifyCodeBtn');
        const codeInput = document.getElementById('codeInput');
        const connectContainer = document.getElementById('connectContainer');
        const paymentMethodForm = document.getElementById('payment-method-form');
        const confirmationForm = document.getElementById('confirmation-form');
        const confirmationDetails = document.getElementById('confirmation-details');
        const connectBtn = document.getElementById('connectBankBtn');
        const accountHolderNameField = document.getElementById('account-holder-name-field');
        const emailField = document.getElementById('email-field');
        const stripe = Stripe('<?php echo htmlspecialchars($stripePublishableKey, ENT_QUOTES, 'UTF-8'); ?>');

        let clientSecret = null;
        let inviteVerified = false;

        const setStatus = (message, tone = 'info') => {
            if (!message) {
                statusEl.className = 'alert d-none mt-3';
                statusEl.textContent = '';
                return;
            }
            statusEl.textContent = message;
            statusEl.className = `alert alert-${tone} mt-3`;
        };

        const scheduleClose = () => {
            setTimeout(() => window.close(), 3000);
        };

        const setPaymentFormEnabled = (isEnabled) => {
            [accountHolderNameField, emailField, connectBtn].forEach((el) => {
                el.disabled = !isEnabled;
            });
        };
        setPaymentFormEnabled(false);

        const verifyCode = async (code) => {
            try {
                const formData = new FormData();
                formData.append('code', code);
                const response = await fetch('./verify_payment_code.php', {
                    method: 'POST',
                    body: formData,
                });

                if (!response.ok) {
                    const text = await response.text();
                    throw new Error(`HTTP ${response.status}: ${text}`);
                }

                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.error || 'Invalid code');
                }
                return true;
            } catch (err) {
                console.error('Error verifying code:', err);
                throw err;
            }
        };

        verifyBtn.addEventListener('click', async () => {
            const code = codeInput.value.trim();
            if (!code) {
                setStatus('Please enter your payment setup code.', 'danger');
                return;
            }
            setStatus('Verifying code...', 'info');
            verifyBtn.disabled = true;
            try {
                await verifyCode(code);
                setStatus('Code verified. You can now connect your bank account.', 'success');
                connectContainer.classList.remove('hidden');
                inviteVerified = true;
                setPaymentFormEnabled(true);
                clientSecret = null; // reset in case a new invite code is used
            } catch (err) {
                setStatus(err.message || 'Invalid code. Please try again.', 'danger');
                alert(err.message || 'Invalid code.');
            } finally {
                verifyBtn.disabled = false;
            }
        });

        const fetchSetupIntent = async () => {
            try {
                const response = await fetch('./customer_billing_add_bank_setupintent.php', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                });

                if (!response.ok) {
                    const text = await response.text();
                    throw new Error(`HTTP ${response.status}: ${text}`);
                }

                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                if (!data.clientSecret) {
                    throw new Error('Missing clientSecret in response');
                }
                return data.clientSecret;
            } catch (err) {
                console.error('Error fetching SetupIntent:', err);
                throw err;
            }
        };

        const renderConfirmationDetails = (setupIntent) => {
            const pm = setupIntent?.payment_method;
            const bank = pm?.us_bank_account || {};
            const billing = pm?.billing_details || {};
            const pieces = [
                bank.bank_name ? `Bank: ${bank.bank_name}` : null,
                bank.last4 ? `Account ending in ${bank.last4}` : null,
                bank.account_type ? `Type: ${bank.account_type}` : null,
                billing.name ? `Name: ${billing.name}` : null,
                billing.email ? `Email: ${billing.email}` : null,
            ].filter(Boolean);
            confirmationDetails.textContent = pieces.join(' • ') || 'Bank account collected. Review mandate to continue.';
        };

        paymentMethodForm.addEventListener('submit', async (ev) => {
            ev.preventDefault();
            if (!inviteVerified) {
                setStatus('Please verify your payment setup code first.', 'danger');
                return;
            }

            const name = accountHolderNameField.value.trim();
            const email = emailField.value.trim();
            if (!name || !email) {
                setStatus('Name and email are required.', 'danger');
                return;
            }

            setStatus('Opening secure bank verification...', 'info');
            setPaymentFormEnabled(false);
            confirmationForm.classList.add('hidden');

            try {
                clientSecret = await fetchSetupIntent();

                const { setupIntent, error } = await stripe.collectBankAccountForSetup({
                    clientSecret,
                    params: {
                        payment_method_type: 'us_bank_account',
                        payment_method_data: {
                            billing_details: {
                                name,
                                email,
                            },
                        },
                    },
                    expand: ['payment_method'],
                });

                if (error) {
                    throw new Error(error.message || 'Bank account collection failed.');
                }

                if (!setupIntent) {
                    throw new Error('No SetupIntent returned from Stripe.');
                }

                if (setupIntent.status === 'requires_payment_method') {
                    setStatus('Bank connection canceled. You can try again.', 'danger');
                    return;
                }

                if (setupIntent.status === 'requires_confirmation') {
                    renderConfirmationDetails(setupIntent);
                    confirmationForm.classList.remove('hidden');
                    confirmationForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setStatus('Review the bank details and accept the mandate to finish.', 'info');
                    return;
                }

                if (setupIntent.status === 'succeeded') {
                    setStatus('Bank account connected and ready to use. This tab will close in a moment.', 'success');
                    paymentMethodForm.reset();
                    scheduleClose();
                } else {
                    setStatus(`Bank account collection status: ${setupIntent.status}`, 'info');
                }
            } catch (err) {
                console.error('Error during bank account flow:', err);
                setStatus(err.message || 'Something went wrong. Please try again.', 'danger');
                alert(err.message || 'Error connecting bank account.');
            } finally {
                setPaymentFormEnabled(true);
            }
        });

        confirmationForm.addEventListener('submit', async (ev) => {
            ev.preventDefault();
            if (!clientSecret) {
                setStatus('Missing client secret. Please restart the connection flow.', 'danger');
                return;
            }

            const submitBtn = confirmationForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            setStatus('Confirming your bank account...', 'info');

            try {
                const { setupIntent, error } = await stripe.confirmUsBankAccountSetup(clientSecret);
                if (error) {
                    throw new Error(error.message || 'Confirmation failed.');
                }

                if (setupIntent.status === 'succeeded') {
                    setStatus('Bank account saved and ready for payments. You may now close your browser.', 'success');
                    confirmationForm.classList.add('hidden');
                    paymentMethodForm.reset();
                    scheduleClose();
                } else {
                    setStatus(`SetupIntent status: ${setupIntent.status}`, 'info');
                }
            } catch (err) {
                console.error('Error confirming bank account:', err);
                setStatus(err.message || 'Something went wrong. Please try again.', 'danger');
                alert(err.message || 'Error confirming bank account.');
            } finally {
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
