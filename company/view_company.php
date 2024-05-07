<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

$message = filter_input(INPUT_GET, 'message');

$state_ids = [
    "AL", // Alabama
    "AK", // Alaska
    "AZ", // Arizona
    "AR", // Arkansas
    "CA", // California
    "CO", // Colorado
    "CT", // Connecticut
    "DE", // Delaware
    "FL", // Florida
    "GA", // Georgia
    "HI", // Hawaii
    "ID", // Idaho
    "IL", // Illinois
    "IN", // Indiana
    "IA", // Iowa
    "KS", // Kansas
    "KY", // Kentucky
    "LA", // Louisiana
    "ME", // Maine
    "MD", // Maryland
    "MA", // Massachusetts
    "MI", // Michigan
    "MN", // Minnesota
    "MS", // Mississippi
    "MO", // Missouri
    "MT", // Montana
    "NE", // Nebraska
    "NV", // Nevada
    "NH", // New Hampshire
    "NJ", // New Jersey
    "NM", // New Mexico
    "NY", // New York
    "NC", // North Carolina
    "ND", // North Dakota
    "OH", // Ohio
    "OK", // Oklahoma
    "OR", // Oregon
    "PA", // Pennsylvania
    "RI", // Rhode Island
    "SC", // South Carolina
    "SD", // South Dakota
    "TN", // Tennessee
    "TX", // Texas
    "UT", // Utah
    "VT", // Vermont
    "VA", // Virginia
    "WA", // Washington
    "WV", // West Virginia
    "WI", // Wisconsin
    "WY", // Wyoming
    "DC"  // District of Columbia
];

include '../view/header.php';
?>

<main>

    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #FFFFFF;">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ml-auto">
                <li id="action_dropdown" class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <form action="index.php" method="post">
                            <input type="hidden" name="action" value="delete_company">
                            <input type="hidden" name="company_id" value="<?php echo $company_info['CompanyId']; ?>">
                            <input type="submit" class="dropdown-item" value="Delete">
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <br>
    <h3><?php echo $company_info['Name']; ?></h3>
    <br>
    <!--
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
-->
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Company Name &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_name_modal">Edit</a></small></th>
                <th scope="col">Address 1 &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_address1_modal">Edit</a></small></th>
                <th scope="col">Address 2 &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_address2_modal">Edit</a></small></th>
                <th scope="col">Address 3 &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_address3_modal">Edit</a></small></th>
                <th scope="col">City &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_city_modal">Edit</a></small></th>
                <th scope="col">State &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_state_id_modal">Edit</a></small></th>
                <th scope="col">Zip &nbsp; <small><a href="" data-toggle="modal" data-target="#edit_zip_modal">Edit</a></small></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $company_info['Name']; ?>
                <td><?php echo $company_info['Address1']; ?> 
                <td><?php echo $company_info['Address2']; ?>
                <td><?php echo $company_info['Address3']; ?>
                <td><?php echo $company_info['City']; ?>
                <td><?php echo $company_info['StateId']; ?> 
                <td><?php echo $company_info['Zip']; ?>
            </tr>
        </tbody>
    </table>
    <br><br>
    <p class="text-danger">
        <?php echo $message; ?>
    </p>

    <div class="modal fade" id="edit_name_modal" tabindex="-1" role="dialog" aria-labelledby="edit_name_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_name_modal_label">Edit Company Name</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_name">
                        <input type="hidden" name="company_id" value="<?php echo $company_info['CompanyId']; ?>">
                        <div class="form-group">
                            <label for="new_name" class="col-form-label">New Company Name:</label>
                            <input type="text" name="new_name" id="new_name">
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
    
    <div class="modal fade" id="edit_address1_modal" tabindex="-1" role="dialog" aria-labelledby="edit_address1_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_address1_modal_label">Edit Address1</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_address1">
                        <input type="hidden" name="company_id" value="<?php echo $company_info['CompanyId']; ?>">
                        <div class="form-group">
                            <label for="new_address1" class="col-form-label">New Address1:</label>
                            <input type="text" name="new_address1" id="new_address1">
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

    <div class="modal fade" id="edit_address2_modal" tabindex="-1" role="dialog" aria-labelledby="edit_address2_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_address2_modal_label">Edit Address2</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_address2">
                        <input type="hidden" name="company_id" value="<?php echo $company_info['CompanyId']; ?>">
                        <div class="form-group">
                            <label for="new_address2" class="col-form-label">New Address2:</label>
                            <input type="text" name="new_address2" id="new_address2">
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

    <div class="modal fade" id="edit_address3_modal" tabindex="-1" role="dialog" aria-labelledby="edit_address3_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_address3_modal_label">Edit Address3</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_address3">
                        <input type="hidden" name="company_id" value="<?php echo $company_info['CompanyId']; ?>">
                        <div class="form-group">
                            <label for="new_address3" class="col-form-label">New Address3:</label>
                            <input type="text" name="new_address3" id="new_address3">
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

    <div class="modal fade" id="edit_city_modal" tabindex="-1" role="dialog" aria-labelledby="edit_city_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_city_modal_label">Edit City</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_city">
                        <input type="hidden" name="company_id" value="<?php echo $company_info['CompanyId']; ?>">
                        <div class="form-group">
                            <label for="new_city" class="col-form-label">New City:</label>
                            <input type="text" name="new_city" id="new_city">
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

    <div class="modal fade" id="edit_state_id_modal" tabindex="-1" role="dialog" aria-labelledby="edit_state_id_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_state_id_modal_label">Edit State</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_state_id">
                        <input type="hidden" name="company_id" value="<?php echo $company_info['CompanyId']; ?>">
                        <div class="form-group">
                            <label for="new_state_id" class="col-form-label">New State:</label>
                            <select name="new_state_id" id="new_state_id">
                                <?php foreach ($state_ids as $state_id): ?>
                                <option value="<?php echo $state_id; ?>"><?php echo $state_id; ?></option>
                                <?php endforeach; ?>
                            </select>
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

    <div class="modal fade" id="edit_zip_modal" tabindex="-1" role="dialog" aria-labelledby="edit_zip_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_zip_modal_label">Edit Zip</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post">
                        <input type="hidden" name="action" value="edit_zip">
                        <input type="hidden" name="company_id" value="<?php echo $company_info['CompanyId']; ?>">
                        <div class="form-group">
                            <label for="new_zip" class="col-form-label">New Zip:</label>
                            <input type="text" name="new_zip" id="new_zip">
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