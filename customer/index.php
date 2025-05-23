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
require '../model/state_db.php';
$user_id = $_SESSION['userId'];
$customer_db = new CustomerDB;
$log_statements_db = new LogStatementsDB;
$users_db = new UsersDB;
$contracts_db = new ContractsDB;
$states_db = new StateDB;

$user_info = $users_db->get_user_info($user_id);

$action = filter_input(INPUT_POST, 'action');
    if ($action == null) {
        $action = filter_input(INPUT_GET, 'action');
        if ($action == null) {
            header('Location: ../homepage.php');
        }
    }

if ($action == 'list_customers') {
    if ($user_info['superuser'] == 1) {
        $customers = $customer_db->get_customers_sudo();
    } else {
        $customers = $customer_db->get_customers($user_id);
    }
    include 'list_customers.php';
} else if ($action == 'view_customer') {
    $customer_id = filter_input(INPUT_GET, 'customer_id');
    $customer_info = $customer_db->get_customer_info($customer_id);
    $statements = $log_statements_db->get_statements_limit3($customer_id);
    $contracts = $contracts_db->get_contracts_limit3($customer_id);
    include 'view_customer.php';
} else if ($action == 'edit_data') {
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
} else if ($action == 'delete_customer') {
    $selected_ids = $_POST['selected_customers'] ?? [];

    foreach ($selected_ids as $customer_id) {
        $customer_db->delete_customer($customer_id);
    }

    header("Location: index.php?action=list_customers");
} else if ($action == 'create_customer') {
    $type = 'customer';
    $states = $states_db->get_all_states();
    $fields = [
        'Name' => 'text',
        'Address1' => 'text',
        'Address2' => 'text',
        'Address3' => 'text',
        'City' => 'text',
        'StateId' => 'text',
        'Zip' => 'number',
        'Phone' => 'text',
        'Email' => 'email'
    ];
    include '../view/form_create.php';
} else if ($action == 'submit_create') {
    $type = filter_input(INPUT_POST, 'type');

    // Gather all POSTed form data except 'action' and 'type'
    $data = [];
    foreach ($_POST as $key => $value) {
        if ($key !== 'action' && $key !== 'type') {
            $data[$key] = $value;
        }
    }

    if ($type === 'customer') {
        // You need a method that accepts an associative array of fields
        $data['userId'] = $user_id; // Add userId to the data array
        $customer_db->create_customer($data);
    }

    // You could add other types here in the future
    // else if ($type === 'contact') { ... }

    header("Location: index.php?action=list_customers");
}

?>