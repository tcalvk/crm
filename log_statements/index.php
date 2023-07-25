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
    include 'view_statement.php';
}

?>