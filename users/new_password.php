<?php 
session_start();
$message = filter_input(INPUT_GET, 'message');
$user_id = $_SESSION["userId"];
if (!isset($_SESSION["can_change_password"])) {
    header("Location: index.php");
}
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
            <h1>Enter New Password</h1>

            <form action="index.php" method="post" class="aligned">
                <input type="hidden" name="action" value="submit_password">

                <label>New Password:</label>
                <input type="password" class="text" name="new_password">
                <br>

                <label>&nbsp;</label><br>
                <input type="submit" value="Submit">&nbsp;
                <a href="login.php">Login Instead</a>
            </form>
            <div>
                <p><?php echo $message; ?></p>
            </div>
        </main>
    </body>
</html>
