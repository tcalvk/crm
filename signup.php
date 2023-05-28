<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Car Journal</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>    </head>
    <body>
        <div class="container">
            <h2>Corsaire CRM</h2>
            <h3>Sign Up</h3>
            <form action="." method="post" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="check_signup">

                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input class="form-control" id="first_name" type="text" name="first_name" required>
                    <div class="invalid-feedback">
                        You must enter a first name. 
                    </div>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input class="form-control" id="last_name" type="text" name="last_name" required>
                    <div class="invalid-feedback">
                        You must enter a last name. 
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback">
                        You must enter an email address. 
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="invalid-feedback">
                        You must enter a password. 
                    </div>
                </div>
                <input class="btn btn-primary" type="submit" value="Sign Up">&nbsp;
                <a href="login.php">Login Instead</a>
            </form>

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
        </div>
    </body>