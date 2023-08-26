<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
require '../model/database.php';
require '../model/log_statements_db.php';
$user_id = $_SESSION['userId'];
$log_statements_db = new LogStatementsDB;

$action = filter_input(INPUT_POST, 'action');
if ($action == null) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == null) {
        header('Location: ../homepage.php');
    }
} 

if ($action == 'view_statement') {
    $statement_number = filter_input(INPUT_GET, 'statement_number');
    $statement = $log_statements_db->get_statement($statement_number);
    if ($statement['WrittenOff'] > 0) {
        $display = 0;
    } else {
        $display = 1;
    }
    include 'view_statement.php';
} else if ($action == 'view_all') {
    $customer_id = filter_input(INPUT_GET, 'customer_id');
    $statements = $log_statements_db->get_statements($customer_id);
    include 'view_statements_list.php';
} else if ($action == 'mark_paid') {
    $statement_number = filter_input(INPUT_POST, 'statement_number');
    $paid_date = filter_input(INPUT_POST, 'paid_date');
    $success = $log_statements_db->mark_as_paid($statement_number, $paid_date);
    header('Location: .?action=view_statement&statement_number='.$statement_number);
} else if ($action == 'clear_paid_date') {
    $statement_number = filter_input(INPUT_POST, 'statement_number');
    $success = $log_statements_db->clear_paid_date($statement_number);
    header('Location: .?action=view_statement&statement_number='.$statement_number);
} else if ($action == 'write_off') {
    $statement_number = filter_input(INPUT_POST, 'statement_number');
    $success = $log_statements_db->write_off_statement($statement_number);
    $success2 = $log_statements_db->clear_paid_date($statement_number);
    header('Location: .?action=view_statement&statement_number='.$statement_number);
}

?>