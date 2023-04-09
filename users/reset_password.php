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
            <h1>Reset Password</h1>

            <form action="index.php" method="post" class="aligned">
                <input type="hidden" name="action" value="send_code">

                <label>Email:</label>
                <input type="text" class="text" name="email">
                <br>

                <label>&nbsp;</label><br>
                <input type="submit" value="Send Reset Code">&nbsp;
                <a href="login.php">Login Instead</a>
            </form>
            <div>
                <p><?php echo $message; ?></p>
            </div>
        </main>
    </body>
</html>
