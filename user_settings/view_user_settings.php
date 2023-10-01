<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <br><br>
    <h5>User Information</h5>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">First Name &nbsp; <small><a href="">Edit</a></small></th>
                <th scope="col">Last Name</th>
            </tr>
        </thead>
    </table>

    <!---->
    <!---->
    <!--Hidden Modals below-->
    <!---->
    <!---->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Mark Statement as Paid</h5>
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
                            <span class="input-group-addon">$</span>
                            <input type="number" value="<?php echo $statement['PaymentAmount']; ?>" min="0" step="0.01" name="payment_amount" id="payment_amount" class="form-control currency">
                        </div>
                </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary" value="Save">
                    </form>
                    </div>
            </div>
        </div>
    </div>


</main>




<?php include '../view/footer.php'; ?>