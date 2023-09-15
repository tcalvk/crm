<?php 
session_start();
include 'view/header.php'; 
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
?>
<main>
    <h1>Menu</h1>
</main>

<?php include 'view/footer.php'; ?>