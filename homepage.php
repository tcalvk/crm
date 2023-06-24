<?php 
session_start();
include 'view/header.php'; 
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
?>
<main>
    <h1>Menu</h1>
    <ul>
        <li>
            <a href="lists/">Shopping Lists</a>
        </li>
        <li>
            <a href="setting/index.php?action=setting_list">Settings</a>
        </li>
    </ul>
</main>

<?php include 'view/footer.php'; ?>