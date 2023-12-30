<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
$user_id = $_SESSION['userId'];
require '../model/database.php';
require '../model/users_db.php';
require '../model/contracts_db.php';
$contracts_db = new ContractsDB;
$users_db = new UsersDB;


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
    //If user is a superuser, bypass all security and display the record
    if ($user_info['superuser'] == 1) {
        include 'view_contract.php';
    } else {
        //If user owns the record, display the record
        $contract_info = $contracts_db->get_contract_info($contract_id);
    }
}

?>