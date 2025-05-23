<?php 
require __DIR__ . "/vendor/autoload.php";
require 'model/email_server.php';
require 'model/database.php';
require 'model/contracts_db.php';
require 'model/log_statements_db.php';
require 'model/contact_db.php';
use Dompdf\Dompdf;
use Dompdf\Options;
$email_server = new EmailServer;
$contracts_db = new ContractsDB;
$log_statements_db = new LogStatementsDB;
$contact_db = new ContactDB;

// Get statement info
$contracts = $contracts_db->get_test_evergreen_contracts();

// Generate pdf 
foreach ($contracts as $contract) :

    //Get the billing contacts
    $contract_id = $contract['ContractId'];
    $contacts = $contact_db->get_contacts($contract_id);


    //Get the email addresses for the contacts
    ////
    foreach ($contacts as $contact) : 
        $email_recipients[] = $contact['Email'];
    endforeach;
    ////
    ////
    $options = new Options;
    $options->setChroot(__DIR__);
    $pdf = new Dompdf($options);

    $invoice_number = $log_statements_db->generate_statement_number();
    $due_month = strtotime("+1 months", strtotime(date("y-m-d")));
    $due_month = date("Y/m", $due_month);
    $due_day = $contract['DueDate'];
    $current_date = date("Y/m/d");
    $property_id = $contract['PropertyId'];
    $date = new DateTime;
    $date_file = $date->format("Y-m-d");
    $date_formatted = $date->format("m/d/Y");
    $company_name = $contract['CompanyName'];
    $company_address = $contract['CompanyAddress1'] . $contract['CompanyAddress2'] . $contract['CompanyAddress3'];
    $company_state = $contract['CompanyState'];
    $company_city = $contract['CompanyCity'];
    $company_zip = $contract['CompanyZip'];
    $billing_name = $contract['BillingName'];
    $attention = $contract['Attention'];
    $billing_address = $contract['BillingAddress1'] . $contract['BillingAddress2'] . $contract['BillingAddress3'];
    $billing_email = $contract['BillingEmail'];
    $billing_city = $contract['BillingCity'];
    $billing_state = $contract['BillingState'];
    $billing_zip = $contract['BillingZip'];
    $base_amt = $contract['BaseAmt'];
    $cam = $contract['CAM'];
    $total = $contract['Total'];

    $html = file_get_contents("tmplt.html");
    $html = str_replace("{{DueMonth}}", $due_month, $html);
    $html = str_replace("{{DueDay}}", $due_day, $html);
    $html = str_replace("{{InvoiceNumber}}", $invoice_number, $html);
    $html = str_replace("{{CurrentDate}}", $current_date, $html);
    $html = str_replace("{{CompanyName}}", $company_name, $html);
    $html = str_replace("{{CompanyAddress}}", $company_address, $html);
    $html = str_replace("{{CompanyCity}}", $company_city, $html);
    $html = str_replace("{{CompanyState}}", $company_state, $html);
    $html = str_replace("{{CompanyZip}}", $company_state, $html);
    $html = str_replace("{{BillingName}}", $billing_name, $html);
    $html = str_replace("{{Attention}}", $attention, $html);
    $html = str_replace("{{BillingAddress}}", $billing_address, $html);
    $html = str_replace("{{BillingCity}}", $billing_city, $html);
    $html = str_replace("{{BillingState}}", $billing_state, $html);
    $html = str_replace("{{BillingZip}}", $billing_zip, $html);
    $html = str_replace("{{BillingEmail}}", $billing_email, $html);
    $html = str_replace("{{BaseAmt}}", $base_amt, $html);
    $html = str_replace("{{CAM}}", $cam, $html);
    $html = str_replace("{{Total}}", $total, $html);

    $pdf->loadHtml($html);
    $pdf->render();

    $output = $pdf->output();
    file_put_contents("statements/" . "property" . $property_id . "_" . $date_file . ".pdf", $output);
    unset($pdf);

    // get email password 
    $email_account_type = 'external';
    $email_password = $email_server->get_email_password($email_account_type);
    $send_email = $email_server->send_statement_test($email_recipients, $property_id, $date_file, $date_formatted, $company_name, $email_password);

    // Log the statement in the database
    $completed_date = date("Y-m-d");
    $due_date_string = $due_month . '/' . $due_day;
    $due_date = date("Y-m-d", strtotime($due_date_string));
    $contract_id = $contract['ContractId'];
    $log_statement = $log_statements_db->log_evergreen_statement($invoice_number, $completed_date, $total, $contract_id, $due_date, $base_amt, $cam);
    unset($email_recipients);
    // Send a courtesy email notification
    $contract_owner_email = $contract['ContractOwnerEmail'];

    //get email password 
    $email_account_type = 'internal';
    $email_password = $email_server->get_email_password($email_account_type);
    $email_notification = $email_server->statements_sent_notification($contract_owner_email, $email_password);
    unset($contract_owner_email);
endforeach; 
?>