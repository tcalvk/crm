<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
require '../model/database.php';
require '../model/customer_db.php';
require '../model/log_statements_db.php';
require '../model/users_db.php';
require '../model/contracts_db.php';
require '../model/companies_db.php';
$user_id = $_SESSION['userId'];
$customer_db = new CustomerDB;
$log_statements_db = new LogStatementsDB;
$users_db = new UsersDB;
$contracts_db = new ContractsDB;
$companies_db = new CompaniesDB;

$user_info = $users_db->get_user_info($user_id);

$action = filter_input(INPUT_POST, 'action');
    if ($action == null) {
        $action = filter_input(INPUT_GET, 'action');
        if ($action == null) {
            header('Location: ../homepage.php');
        }
    }

if ($action == 'list_companies') {
    if ($user_info['superuser'] == 1) {
        $companies = $companies_db->get_companies_sudo();
    } else {
        $companies = $companies_db->get_companies($user_id);
    }
    include 'list_companies.php';


} else if ($action == 'view_company') {
    $company_id = filter_input(INPUT_GET, 'company_id');
    $company_info = $companies_db->get_company_info($company_id);
    include 'view_company.php';
} else if ($action == 'create_new_company') {
    $name = filter_input(INPUT_POST, 'name'); 
    $address1 = filter_input(INPUT_POST, 'address1'); 
    $address2 = filter_input(INPUT_POST, 'address2'); 
    $address3 = filter_input(INPUT_POST, 'address3'); 
    $city = filter_input(INPUT_POST, 'city'); 
    $state_id = filter_input(INPUT_POST, 'state_id'); 
    $zip = filter_input(INPUT_POST, 'zip'); 
    $user_id = filter_input(INPUT_POST, 'user_id');
    
    $create_company = $companies_db->create_company($name, $address1, $address2, $address3, $city, $state_id, $zip, $user_id);
    header("Location: .?action=list_companies");
}
    /*else if ($action == 'edit_data') {
    $data_type = filter_input(INPUT_POST, 'date_type');
    $new_value = filter_input(INPUT_POST, 'new_value');
    $customer_id = filter_input(INPUT_POST, 'customer_id');
    switch ($data_type) {
        case 'name':
            $customer_db->update_name($customer_id, $new_value);
            break;
        case 'address1':
            $customer_db->update_address1($customer_id, $new_value);
            break;
        case 'address2':
            $customer_db->update_address2($customer_id, $new_value);
            break;
        case 'address3':
            $customer_db->update_address3($customer_id, $new_value);
            break;
        case 'city':
            $customer_db->update_city($customer_id, $new_value);
            break;
        case 'state_id':
            $customer_db->update_state_id($customer_id, $new_value);
            break;
        case 'zip':
            $customer_db->update_zip($customer_id, $new_value);
            break;
        case 'phone':
            $customer_db->update_phone($customer_id, $new_value);
            break;
        case 'email':
            $customer_db->update_email($customer_id, $new_value);
            break;
    }
    header("Location: index.php?action=view_customer&customer_id=".$customer_id);

}
    */

?>