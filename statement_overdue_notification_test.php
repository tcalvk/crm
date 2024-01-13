<?php 
require __DIR__ . "/vendor/autoload.php";
require 'model/database.php';
require 'model/email_server.php';
require 'model/log_statements_db.php';
$email_server = new EmailServer;
$log_statements_db = new LogStatementsDB;

$overdue_statements = $log_statements_db->get_overdue_statements_test();

foreach ($overdue_statements as $overdue_statement) :
    $user_first_name = $overdue_statement['UserFirstName'];
    $statement_number = $overdue_statement['StatementNumber'];
    $customer_name = $overdue_statement['CustomerName'];
    $contract_name = $overdue_statement['ContractName'];
    $due_date = $overdue_statement['DueDate'];
    $created_date = $overdue_statement['CreatedDate'];
    $user_email = $overdue_statement['email'];

    $send_notification = $email_server->statement_overdue_notification($user_email, $statement_number, $customer_name, $contract_name, $created_date, $due_date);

    $update_statement = $log_statements_db->update_overdue_statements($statement_number);
    
    unset($user_email);
endforeach;

?>
