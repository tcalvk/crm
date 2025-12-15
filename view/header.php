<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/dc50476e0d.js" crossorigin="anonymous"></script>
    <style>
.hide {
  display: none;
}
    
.myDIV:hover + .hide {
  display: block;
}
</style>
    </head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="/homepage.php">Corsaire CRM</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="/homepage.php">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/company/index.php?action=list_companies">Companies</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/index.php?action=list_customers">Customers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/property/index.php?action=list_properties">Properties</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="/user_settings/index.php?action=view_user_settings" aria-label="Settings">
                            <i class="fas fa-cog" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <form class="form-inline my-0" action="/index.php" method="post">
                            <input type="hidden" name="action" value="logout">
                            <input class="btn btn-outline-secondary btn-sm" type="submit" value="Logout">
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        
        
