<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
require '../model/database.php';


$action = filter_input(INPUT_POST, 'action');
if ($action == null) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == null) {
        header('Location: ../homepage.php');
    }
}

if ($action == 'view_contract') {
    // Get the contract id from the get/post
    $contract_id = filter_input(INPUT_GET, 'contract_id'); 
    include 'view_contract'; 
}

?>