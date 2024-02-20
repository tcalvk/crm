<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
$user_id = $_SESSION['userId'];
require '../model/database.php';
require '../model/users_db.php';
require '../model/contracts_db.php';
require '../model/customer_db.php';
require '../model/contract_term_db.php';
$contracts_db = new ContractsDB;
$users_db = new UsersDB;
$customer_db = new CustomerDB;
$contract_term_db = new ContractTermDB;

$user_info = $users_db->get_user_info($user_id);
$action = filter_input(INPUT_POST, 'action');
if ($action == null) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == null) {
        header('Location: ../homepage.php');
    }
}

if ($action == 'view_contract') {
    $contract_id = filter_input(INPUT_GET, 'contract_id');   
    $current_term = $contract_term_db->get_current_term($contract_id);
    $contract_terms = $contract_term_db->get_all_terms($contract_id);
    $contract_info = $contracts_db->get_contract_info($contract_id);  
    //If user is a superuser, bypass all security and display the record
    if ($user_info['superuser'] == 1) {
        include 'view_contract.php';
    //If user owns the record, display the record
    } else if ($user_id == $contract_info['userId']) {
        include 'view_contract.php';
    } else {
        header('Location: view/record_access_error.php');
    }
} else if ($action == 'view_contracts_list') {
    $customer_id = filter_input(INPUT_GET, 'customer_id');
    $customer_info = $customer_db->get_customer_info($customer_id);
    $contracts = $contracts_db->get_contracts($customer_id);
    if ($user_info['superuser'] == 1) {
        include 'view_contracts_list.php';
    //If user owns the record, display the record
    } else if ($user_id == $customer_info['userId']) {
        include 'view_contracts_list.php';
    } else {
        header('Location: view/record_access_error.php');
    }
}

?>