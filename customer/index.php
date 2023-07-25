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
} else if ($action == 'view_customer') {
    $customer_id = filter_input(INPUT_GET, 'customer_id');
    $customer_info = $customer_db->get_customer_info($customer_id);
    include 'view_customer.php';
} else if ($action == 'edit_data') {
    $data_type = filter_input(INPUT_POST, 'data_type');
    $new_data = filter_input(INPUT_POST, 'new_data');
    $customer_id = filter_input(INPUT_POST, 'customer_id');
    header("Location:index.php?action=view_customer&customer_id=".$customer_id);
}


?>