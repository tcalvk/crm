<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
require '../model/database.php';
require '../model/log_statements_db.php';
require '../model/log_fixed_payments_db.php';
require '../model/contracts_db.php';
$user_id = $_SESSION['userId'];
$log_statements_db = new LogStatementsDB;
$log_fixed_payments_db = new LogFixedPaymentsDB;
$contracts_db = new ContractsDB;
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
    if ($statement['Status'] == 'Paid') {
        $display_markpaid = 0;
    } else if ($statement['Status'] == 'Partial Payment') {
        $display_markpaid = 0;
    } else {
        $display_markpaid = 1;
    }
    if ($statement['StatementAutoReceive'] == 'true') {
        $statement_auto_receive = 'This statement will be auto received on ' . $statement['DueDate'] . '.';
    } else {
        $statement_auto_receive = 'This statement will not be auto received.';
    }
    include 'view_statement.php';
} else if ($action == 'view_all') {
    $customer_id = filter_input(INPUT_GET, 'customer_id');
    $statements = $log_statements_db->get_statements($customer_id);
    include 'view_statements_list.php';
} else if ($action == 'mark_paid') {
    $statement_number = filter_input(INPUT_POST, 'statement_number');
    $paid_date = filter_input(INPUT_POST, 'paid_date');
    $payment_amount = filter_input(INPUT_POST, 'payment_amount');
    $contract_info = $log_statements_db->get_contract_info($statement_number);
    $success = $log_statements_db->mark_as_paid($statement_number, $paid_date, $payment_amount);
    if ($contract_info['ContractType'] == 'Fixed') {
        //Assign variables for subtract_payment function
        $num_payments_due = $contract_info['NumPaymentsDue'];
        $new_payments_due = $num_payments_due - 1;
        $contract_id = $contract_info['ContractId'];
        $completed_date = date("Y-m-d");
        //
        $success = $log_fixed_payments_db->subtract_payment($contract_id, $completed_date, $num_payments_due, $new_payments_due, $statement_number);
        $update_contract = $contracts_db->update_contract($contract_id, $new_payments_due);
    }
    header('Location: .?action=view_statement&statement_number='.$statement_number);
} else if ($action == 'clear_paid_date') {
    $statement_number = filter_input(INPUT_POST, 'statement_number');
    $success = $log_statements_db->clear_paid_date($statement_number);
    $contract_info = $log_statements_db->get_contract_info($statement_number);
    if ($contract_info['ContractType'] == 'Fixed') {
        //Assign variables for subtract_payment function
        $num_payments_due = $contract_info['NumPaymentsDue'];
        $new_payments_due = $num_payments_due + 1;
        $contract_id = $contract_info['ContractId'];
        $completed_date = date("Y-m-d");
        //
        $success = $log_fixed_payments_db->clear_paid_date($contract_id, $completed_date, $num_payments_due, $new_payments_due, $statement_number);
        $update_contract = $contracts_db->update_contract($contract_id, $new_payments_due);
    }
    header('Location: .?action=view_statement&statement_number='.$statement_number);
} else if ($action == 'write_off') {
    $statement_number = filter_input(INPUT_POST, 'statement_number');
    $success = $log_statements_db->write_off_statement($statement_number);
    $success2 = $log_statements_db->clear_paid_date($statement_number);
    header('Location: .?action=view_statement&statement_number='.$statement_number);
}


?>