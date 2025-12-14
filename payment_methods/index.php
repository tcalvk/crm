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
switch ($action) {
    case 'list_payment_methods':
        $customer_id = filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT);
        $customer_info = $customer_db->get_customer_info($customer_id);
        $payment_methods = $payment_methods_db->get_payment_methods($customer_id);
        include 'view_payment_methods_list.php';
        break;
    case 'toggle_payment_method':
        $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
        $payment_method_id = filter_input(INPUT_POST, 'payment_method_id', FILTER_VALIDATE_INT);
        $enable = filter_input(INPUT_POST, 'enable', FILTER_VALIDATE_INT);
        if ($customer_id !== null && $customer_id !== false && $payment_method_id !== false && $enable !== null && $enable !== false) {
            $payment_methods_db->set_payment_method_enabled($payment_method_id, $enable ? 1 : 0);
        }
        header('Location: index.php?action=list_payment_methods&customer_id=' . $customer_id);
        exit;
    case 'delete_payment_method':
        $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
        $payment_method_id = filter_input(INPUT_POST, 'payment_method_id', FILTER_VALIDATE_INT);
        if ($customer_id !== null && $customer_id !== false && $payment_method_id !== null && $payment_method_id !== false) {
            $payment_methods_db->delete_payment_method($payment_method_id);
        }
        header('Location: index.php?action=list_payment_methods&customer_id=' . $customer_id);
        exit;
    default:
        header('Location: ../homepage.php');
        exit;
}

?>
