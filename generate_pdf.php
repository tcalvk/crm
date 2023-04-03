<?php 
require __DIR__ . "/vendor/autoload.php";
require 'model/email_server.php';
require 'model/database.php';
require 'model/contracts_db.php';
use Dompdf\Dompdf;
use Dompdf\Options;
$email_server = new EmailServer;
$contracts_db = new ContractsDB;

// Get statement info
$contracts = $contracts_db->get_evergreen_contracts();

// Generate pdf 
foreach ($contracts as $contract) :

$options = new Options;
$options->setChroot(__DIR__);
$pdf = new Dompdf($options);

$invoice_number = mt_rand(1000, 9999);
$due_month = strtotime("+1 months", strtotime(date("y-m-d")));
$due_month = date("Y/m", $due_month);
$due_day = $contract['DueDate'];
$current_date = date("Y/m/d");
$property_id = $contract['PropertyId'];
$date = new DateTime;
$date_file = $date->format("Y-m-d");
$date_formatted = $date->format("m/d/Y");
$company_name = $contract['CompanyName'];

$html = file_get_contents("tmplt.html");
$html = str_replace("{{DueMonth}}", $due_month, $html);
$html = str_replace("{{DueDay}}", $due_day, $html);

$pdf->loadHtml($html);
$pdf->render();

$output = $pdf->output();
file_put_contents("statements/" . "property" . $property_id . "_" . $date_file . ".pdf", $output);
unset($pdf);

$email = 'tannerklein5@icloud.com';
$send_email = $email_server->send_statement($email, $property_id, $date_file, $date_formatted, $company_name);

endforeach ; 

?>

