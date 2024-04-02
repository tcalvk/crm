<?php 
require __DIR__ . "/vendor/autoload.php";
require 'model/email_server.php';
require 'model/database.php';
require 'model/log_statements_db.php';
require 'model/log_fixed_payments_db.php';
require 'model/contracts_db.php'; 

$email_server = new EmailServer;
$log_statements_db = new LogStatementsDB;
$log_fixed_payments_db = new LogFixedPaymentsDB;
$contracts_db = new ContractsDB;

$statements = $log_statements_db->get_statements_due_autoreceive(); 

foreach ($statements as $statement):
    $statement_number = $statement['StatementNumber'];
    $payment_amount = $statement['TotalAmt'];
    $paid_date = date("Y-m-d");
    $mark_paid = $log_statements_db->mark_as_paid($statement_number, $paid_date, $payment_amount);

    if ($statement['ContractType'] == 'Fixed') {
        //Assign variables for subtract_payment function
        $num_payments_due = $statement['NumPaymentsDue'];
        $new_payments_due = $num_payments_due - 1;
        $contract_id = $statement['ContractId'];
        $completed_date = date("Y-m-d");
        //
        $success = $log_fixed_payments_db->subtract_payment($contract_id, $completed_date, $num_payments_due, $new_payments_due, $statement_number);
        $update_contract = $contracts_db->update_contract($contract_id, $new_payments_due);
    }
    $contract_name = $statement['ContractName'];
    $customer_name = $statement['CustomerName'];
    $created_date = $statement['CreatedDate'];

    
    $email_address = $statement['email'];
    $send_email = $email_server->statement_auto_received($email_address, $statement_number, $contract_name, $customer_name, $created_date);
    unset($email_address);
endforeach;