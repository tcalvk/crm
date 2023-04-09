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
            <h1>Sign Up</h1>
            <form action="." method="post" class="aligned">
                <input type="hidden" name="action" value="check_signup">

                <label>Name:</label>
                <input type="text" name="name">
                <br>

                <label>Email:</label>
                <input type="text" class="text" name="email">
                <br>

                <label>Password:</label>
                <input type="password" class="text" name="password">
                <br>

                <label>&nbsp;</label><br>
                <input type="submit" value="Sign Up">&nbsp;
                <a href="login.php">Login Instead</a>
            </form>
            <div class="error">
                <p><?php echo $message; ?></p>
            </div>
        </main>
    </body>
</html>