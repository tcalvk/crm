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
            <h1>Account Recovery</h1>

            <form action="index.php" method="post" class="aligned">
                <input type="hidden" name="action" value="submit_code">

                <label>Code:</label>
                <input type="text" class="text" name="entered_code">
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
