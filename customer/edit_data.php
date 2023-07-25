<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
include '../view/header.php';

$data_type = filter_input(INPUT_GET, 'data_type');
$customer_id = filter_input(INPUT_GET, 'customer_id');
$current_data = filter_input(INPUT_GET, 'current_data');
?>

<main>
    <br>
    <h3>Edit <?php echo $data_type; ?></h3>
    <br>
    <form action="index.php" method="post">
        <input type="hidden" name="action" value="edit_data">
        <input type="hidden" name="data_type" value="<?php echo $data_type; ?>">
        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
        <div class="form-group">
            <label for="new_data">New <?php echo $data_type; ?></label>
            <input type="text" name="new_data" id="new_data" class="form-control" placeholder="<?php echo $current_data; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</main>

<?php include '../view/footer.php'; ?>