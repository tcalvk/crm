<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

include '../view/header.php';
?>

<main>
    <br>
    <h3><?php echo $customer_info['Name']; ?></h3>
    <br>
    <p class="h5 d-inline">Details</p> &nbsp; <a data-toggle="collapse" href="#edit_details">Edit Details</a>
    <div class="collapse" id="edit_details"><br>
        <div class="form-group">
            <label for="select_data_type">Select Data To Edit</label>
            <select class="form-control" id="select_data_type">
                <option value="Name">Customer Name</option>
                <option value="Address1">Address 1</option>
                <option value="Address2">Address 2</option>
                <option value="Address3">Address 3</option>
                <option value="City">City</option>
                <option value="StateId">State</option>
                <option value="Zip">Zip</option>
                <option value="Phone">Phone</option>
                <option value="Email">Email</option>
            </select>
        </div>
        <div class="form-group">
            <label for="new_value">New Value</label>
            <input type="text" name="new_value" id="new_value" class="form-control">
        </div>        
        <br>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Customer Name</th>
                <th scope="col">Address 1</th>
                <th scope="col">Address 2</th>
                <th scope="col">Address 3</th>
                <th scope="col">City</th>
                <th scope="col">State</th>
                <th scope="col">Zip</th>
                <th scope="col">Phone</th>
                <th scope="col">Email</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $customer_info['Name']; ?>
                <td><?php echo $customer_info['Address1']; ?> 
                <td><?php echo $customer_info['Address2']; ?>
                <td><?php echo $customer_info['Address3']; ?>
                <td><?php echo $customer_info['City']; ?>
                <td><?php echo $customer_info['StateId']; ?> 
                <td><?php echo $customer_info['Zip']; ?>
                <td><?php echo $customer_info['Phone']; ?>
                <td><?php echo $customer_info['Email']; ?>
            </tr>
        </tbody>
    </table>
</main>

<?php include '../view/footer.php'; ?>