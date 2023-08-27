<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
require '../model/database.php';
require '../model/customer_db.php';
require '../model/log_statements_db.php';
$user_id = $_SESSION['userId'];
$customer_db = new CustomerDB;
$log_statements_db = new LogStatementsDB;

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
    $statements = $log_statements_db->get_statements_limit3($customer_id);
    include 'view_customer.php';
} else if ($action == 'edit_data') {
    $data_type = filter_input(INPUT_POST, 'date_type');
    $new_value = filter_input(INPUT_POST, 'new_value');
    $customer_id = filter_input(INPUT_POST, 'customer_id');
    switch ($data_type) {
        case 'name':
            $customer_db->update_name($customer_id, $new_value);
            break;
        case 'address1':
            $customer_db->update_address1($customer_id, $new_value);
            break;
        case 'address2':
            $customer_db->update_address2($customer_id, $new_value);
            break;
        case 'address3':
            $customer_db->update_address3($customer_id, $new_value);
            break;
        case 'city':
            $customer_db->update_city($customer_id, $new_value);
            break;
        case 'state_id':
            $customer_db->update_state_id($customer_id, $new_value);
            break;
        case 'zip':
            $customer_db->update_zip($customer_id, $new_value);
            break;
        case 'phone':
            $customer_db->update_phone($customer_id, $new_value);
            break;
        case 'email':
            $customer_db->update_email($customer_id, $new_value);
            break;
    }
    header("Location: index.php?action=view_customer&customer_id=".$customer_id);
}


?>