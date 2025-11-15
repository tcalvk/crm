<?php 
if (!isset($message)) {
    $message = filter_input(INPUT_GET, 'message');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CRM - Verify Email</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="py-4">
                <h3>Email Verification</h3>
                <p class="text-muted mb-2">Manage verification for <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
                <p class="text-warning">Note: you must verify this email before you can log in.</p>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Send New Code</h5>
                                <p class="card-text">Request another verification code to be delivered to your inbox.</p>
                                <form action="index.php" method="post">
                                    <input type="hidden" name="action" value="request_verification_code">
                                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                    <button type="submit" class="btn btn-primary">Send Code</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Verify Email</h5>
                                <p class="card-text">Enter the latest code sent to you and verify your account.</p>
                                <form action="index.php" method="post">
                                    <input type="hidden" name="action" value="verify_email">
                                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                                    <div class="form-group">
                                        <label for="verification_code">Verification code</label>
                                        <input type="text" class="form-control" id="verification_code" name="verification_code" required>
                                    </div>
                                    <button type="submit" class="btn btn-success">Verify</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (!empty($message)) : ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <a href="login.php">Back to Login</a>
            </div>
        </div>
    </body>
</html>
