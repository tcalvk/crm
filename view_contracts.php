<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM</title>
    <link rel="stylesheet" href="/mealplan/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
</head>
<body>
    <header>
        <h1>CRM</h1>
        <p>
            <form action="/mealplan/" method="post">
                <input type="hidden" name="action" value="logout">
                <input type="submit" value="Logout">
            </form>
        </p>
    </header>

<main>
    <h1>Settings</h1>
    <aside>
        <!--Display a nav menu-->
        <h2>Menu</h2>
        <nav>
            <ul>
                <a href="../">Home</a><br>
            </ul>
        </nav>
    </aside>

    <section>
        <!--Display a table of vehicles-->
        <h2>Personal Information</h2>
        <table>
            <tr>
                <th>ContractId</th>
                <th>BaseAmt</th>
            </tr>
            <?php foreach ($contracts as $contract) : ?>
            <tr>
                <td><?php echo $contract['ContractId']; ?></td>
                <td><?php echo $contract['BaseAmt']; ?></td>
            </tr>
            <tr>
                <td><a href="index.php?action=change_name">Edit</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <h2>Login Information</h2>
        <table>
            <tr>
                <th>Email</th>
                <th>Password</th>
            </tr>
            <tr>
                <td><?php echo $personal_info['email']; ?></td>
                <td>***********</td>
            </tr>
            <tr>
                <td><a href="index.php?action=change_email">Change</a></td>
                <td><a href="index.php?action=change_password">Change</a></td>
            </tr>
        </table>
    </section>
</main>

    <footer>
        <p class="copyright">
            &copy; <?php echo date("Y"); ?> Corsaire Web, Inc. 
        </p>
        <p class="copyright">Version: 1.1</p>
    </footer>
</body>
</html>