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
    <form action="index.php" method="post">
        <input type="hidden" name="action" value="edit_data">
        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
        <div class="collapse" id="edit_details"><br>
            <div class="form-group">
                <label for="select_data_type">Select Data To Edit</label>
                <select class="form-control" id="select_data_type" name="date_type">
                    <option value="name" id="name">Customer Name</option>
                    <option value="address1" id="address1">Address 1</option>
                    <option value="address2" id="address2">Address 2</option>
                    <option value="address3" id="address3">Address 3</option>
                    <option value="city" id="city">City</option>
                    <option value="state_id" id="state_id">State</option>
                    <option value="zip" id="zip">Zip</option>
                    <option value="phone" id="phone">Phone</option>
                    <option value="email" id="email">Email</option>
                </select>
            </div>
            <div class="form-group">
                <label for="new_value">New Value</label>
                <input type="text" name="new_value" id="new_value" class="form-control">
            </div>
            <input class="btn btn-primary" type="submit" value="Save">        
            <br><br>
        </div>
    </form>
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
    <br><br>

    <div id="accordion">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Contracts
                    </button>
                </h5>
            </div>
            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Contract Name</th>
                                <th scope="col">Contract Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contracts as $contract) : ?>
                            <tr>
                                <td><a href="../contract/index.php?action=view_contract&contract_id=<?php echo $contract['ContractId']; ?>"><?php echo $contract['Name']; ?></a></td>
                                <td><?php echo $contract['ContractType']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <br>
                    <div class="d-flex justify-content-center">
                    <a href="../contract/index.php?action=view_contracts_list&customer_id=<?php echo $customer_id; ?>">View All</a>
                </div>
                </div>
            </div>
        </div>
    <div class="card">
        <div class="card-header" id="headingTwo">
        <h5 class="mb-0">
            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Statements
            </button>
        </h5>
        </div>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
            <div class="card-body">
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
                            <td><a href="../log_statements/index.php?action=view_statement&statement_number=<?php echo $statement['StatementNumber']; ?>"><?php echo $statement['StatementNumber']; ?></a>
                            <td><?php echo $statement['CreatedDate']; ?> 
                            <td>$<?php echo $statement['TotalAmt']; ?>
                            <td><?php echo $statement['PropertyName']; ?></td>
                            <td><?php echo $statement['Address1']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
                <div class="d-flex justify-content-center">
                    <a href="../log_statements/index.php?action=view_all&customer_id=<?php echo $customer_id; ?>">View All</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingThree">
        <h5 class="mb-0">
            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Contacts (coming soon)
            </button>
        </h5>
        </div>
        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
        <div class="card-body">
            Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
        </div>
        </div>
    </div>
</div>
</main>

<?php include '../view/footer.php'; ?>