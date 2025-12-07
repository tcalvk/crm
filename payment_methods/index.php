<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
require '../model/database.php';
require '../model/customer_db.php';
require '../model/log_statements_db.php';
require '../model/users_db.php';
require '../model/contracts_db.php';
require '../model/state_db.php';
require '../model/payment_methods_db.php';
$user_id = $_SESSION['userId'];
$customer_db = new CustomerDB;
$log_statements_db = new LogStatementsDB;
$users_db = new UsersDB;
$contracts_db = new ContractsDB;
$states_db = new StateDB;
$payment_methods_db = new PaymentMethodsDB;

$user_info = $users_db->get_user_info($user_id);

$action = filter_input(INPUT_POST, 'action');
    if ($action == null) {
        $action = filter_input(INPUT_GET, 'action');
        if ($action == null) {
            header('Location: ../homepage.php');
        }
    }
if ($action == 'list_payment_methods') {
    $customer_id = filter_input(INPUT_GET, 'customer_id');
    $customer_info = $customer_db->get_customer_info($customer_id);
    $payment_methods = $payment_methods_db->get_payment_methods($customer_id);
    include 'view_payment_methods_list.php';
}

?>
