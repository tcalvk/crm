<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

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
                    <button type="button" class="dropdown-item" id="new_company_button" data-toggle="modal" data-target="#new_company_modal">New</button>
                    <!-- <button type="button" class="dropdown-item" data-toggle="modal" data-target="#writeoffModal">Write Off Statement</button> -->
                </div>
            </li>
        </ul>
    </div>
    </nav>

    <h2>Companies</h2>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($companies as $company) : ?>
            <tr>
                <td><a href="index.php?action=view_company&company_id=<?php echo $company['CompanyId']; ?>"><?php echo $company['Name']; ?></a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <div class="modal fade" id="new_company_modal" tabindex="-1" role="dialog" aria-labelledby="new_company_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="new_company_modal_label">Create New Company</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="action" value="create_new_company">
                        <input type="hidden" name="user_id" value="<?php echo $user_info['userId']; ?>">
                        <div class="form-group">
                            <label for="name" class="col-form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback">
                                You must enter a Name. 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address1" class="col-form-label">Address 1</label>
                            <input type="text" class="form-control" id="address1" name="address1" required>
                            <div class="invalid-feedback">
                                You must enter an Address1. 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address2" class="col-form-label">Address 2</label>
                            <input type="text" class="form-control" id="address2" name="address2">
                        </div>
                        <div class="form-group">
                            <label for="address3" class="col-form-label">Address 3</label>
                            <input type="text" class="form-control" id="address3" name="address3">
                        </div>
                        <div class="form-group">
                            <label for="city" class="col-form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                            <div class="invalid-feedback">
                                You must enter a City. 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="state_id" class="col-form-label">State</label>
                            <select name="state_id" id="state_id" required>
                                <?php foreach ($state_ids as $state_id): ?>
                                <option value="<?php echo $state_id; ?>"><?php echo $state_id; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                You must enter a State. 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="zip" class="col-form-label">Zip</label>
                            <input type="text" class="form-control" id="zip" name="zip" required>
                            <div class="invalid-feedback">
                                You must enter a Zip. 
                            </div>
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

    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
            });
        }, false);
        })();
    </script>

</main>

<?php include '../view/footer.php'; ?>