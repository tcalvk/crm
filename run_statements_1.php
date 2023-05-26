<?php 
///
/// When executed, this php script will query and send all statements that are due to be sent on the 1st of each month. 
///
require __DIR__ . "/vendor/autoload.php";
require 'model/email_server.php';
require 'model/database.php';
require 'model/contracts_db.php';
require 'model/log_fixed_payments_db.php';
require 'model/log_statements_db.php';
use Dompdf\Dompdf;
use Dompdf\Options;
$email_server = new EmailServer;
$contracts_db = new ContractsDB;
$log_fixed_payments_db = new LogFixedPaymentsDB;
$log_statements_db = new LogStatementsDB; 

//For fixed contracts//
// Get statement info 
$contracts = $contracts_db->get_fixed_1();

// Generate pdf 
foreach ($contracts as $contract) :

$options = new Options;
$options->setChroot(__DIR__);
$pdf = new Dompdf($options);

$invoice_number = mt_rand(1000, 9999);
$due_month = date("Y/m");
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
$billing_address = $contract['BillingAddress1'] . ' ' . $contract['BillingAddress2'] . ' ' . $contract['BillingAddress3'];
$billing_city = $contract['BillingCity'];
$billing_state = $contract['BillingState'];
$billing_zip = $contract['BillingZip'];
$billing_email = $contract['BillingEmail'];
$base_amt = $contract['BaseAmt'];
$cam = $contract['CAM'];
$total = $contract['Total'];
$property_name = $contract['PropertyName'];
$num_payments_due = $contract['NumPaymentsDue'];
$payment_number = $contract['TotalPaymentsDue'] - $contract['NumPaymentsDue'] + 1;
$total_payments_due = $contract['TotalPaymentsDue'];
$contract_id = $contract['ContractId'];

$html = file_get_contents("tmplt_fixed.html");
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
$html = str_replace("{{PaymentNumber}}", $payment_number, $html);
$html = str_replace("{{TotalPaymentsDue}}", $total_payments_due, $html);
$html = str_replace("{{PropertyName}}", $property_name, $html);

$pdf->loadHtml($html);
$pdf->render();

$output = $pdf->output();
file_put_contents("statements/" . "property" . $property_id . "_" . $date_file . ".pdf", $output);
unset($pdf);

$send_email = $email_server->send_statement($billing_email, $property_id, $date_file, $date_formatted, $company_name);

$completed_date = date("Y-m-d");
$new_payments_due = $num_payments_due - 1;
$log_payment = $log_fixed_payments_db->subtract_payment($contract_id, $completed_date, $num_payments_due, $new_payments_due);
$update_contract = $contracts_db->update_contract($contract_id, $new_payments_due);
$log_statement = $log_statements_db->log_fixed_statement($invoice_number, $completed_date, $total, $payment_number, $contract_id);

endforeach ; 

?>

