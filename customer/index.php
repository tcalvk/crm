<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
require '../model/database.php';
require '../model/customer_db.php';
$user_id = $_SESSION['userId'];
$customer_db = new CustomerDB;


$action = filter_input(INPUT_POST, 'action');
    if ($action == null) {
        $action = filter_input(INPUT_GET, 'action');
        if ($action == null) {
            header('Location: ../homepage.php');
        }
    }

if ($action == 'list_customers') {
    $customers = $customer_db->get_customers($user_id);
    include 'list_customers.php';
}


?>