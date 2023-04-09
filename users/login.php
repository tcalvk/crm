<?php 
$message = filter_input(INPUT_GET, 'message');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Meal Plan</title>
        <link rel="stylesheet" href="/mealplan/main.css"/>
    </head>
    <body>
        <header>
            <h1>Meal Plan</h1>
        </header>
        <main>
            <h1>Login</h1>

            <form action="index.php" method="post" class="aligned">
                <input type="hidden" name="action" value="check_login">

                <label>Email:</label>
                <input type="text" class="text" name="email">
                <br>

                <label>Password:</label>
                <input type="password" class="text" name="password">
                <br><br>

                <label>&nbsp;</label>
                <input type="submit" value="Login">&nbsp;
                <a href=".?action=forgot_password">Forgot Password</a>

                <br><br>
                <a href=".?action=signup">Sign Up</a>
            </form>
            <div>
                <p><?php echo $message; ?></p>
            </div>
        </main>
    </body>
</html>
