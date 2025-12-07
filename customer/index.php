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
require '../model/payment_methods_db.php';
require '../model/contact_db.php';
$user_id = $_SESSION['userId'];
$customer_db = new CustomerDB;
$log_statements_db = new LogStatementsDB;
$users_db = new UsersDB;
$contracts_db = new ContractsDB;
$states_db = new StateDB;
$payment_methods_db = new PaymentMethodsDB;
$contact_db = new ContactDB;

$user_info = $users_db->get_user_info($user_id);

function user_can_access_customer($user_info, $customer_info) {
    return $user_info['superuser'] == 1 || $user_info['userId'] == $customer_info['userId'];
}

function collect_customer_payload() {
    return [
        'Name' => trim((string) filter_input(INPUT_POST, 'Name')),
        'Address1' => trim((string) filter_input(INPUT_POST, 'Address1')),
        'Address2' => trim((string) filter_input(INPUT_POST, 'Address2')),
        'Address3' => trim((string) filter_input(INPUT_POST, 'Address3')),
        'City' => trim((string) filter_input(INPUT_POST, 'City')),
        'StateId' => trim((string) filter_input(INPUT_POST, 'StateId')),
        'Zip' => trim((string) filter_input(INPUT_POST, 'Zip')),
    ];
}

function validate_customer_payload($data, $states) {
    $errors = [];
    if ($data['Name'] === '') {
        $errors[] = 'Customer name is required.';
    }
    if ($data['Address1'] === '') {
        $errors[] = 'Address 1 is required.';
    }
    if ($data['City'] === '') {
        $errors[] = 'City is required.';
    }
    $state_ids = array_map(function ($state) {
        return $state['StateId'];
    }, $states);
    if ($data['StateId'] === '' || !in_array($data['StateId'], $state_ids)) {
        $errors[] = 'Select a valid state.';
    }
    if ($data['Zip'] === '') {
        $errors[] = 'Zip is required.';
    } else if (!preg_match('/^[0-9\\-\\s]{4,10}$/', $data['Zip'])) {
        $errors[] = 'Enter a valid zip code.';
    }
    return $errors;
}

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
    $customer_id = filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info || !user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $statements = $log_statements_db->get_statements_limit3($customer_id);
    $contracts = $contracts_db->get_contracts_by_customer($customer_id, 5);
    $payment_methods = $payment_methods_db->get_payment_methods_limit3($customer_id);
    $contacts = $contact_db->get_contacts_by_customer($customer_id, 5);
    include 'view_customer.php';
} else if ($action == 'edit_customer') {
    $customer_id = filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT);
    $customer = $customer_db->get_customer_info($customer_id);
    if (!$customer || !user_can_access_customer($user_info, $customer)) {
        include '../view/record_access_error.php';
        exit;
    }
    $states = $states_db->get_all_states();
    $errors = [];
    include 'customer_edit.php';
} else if ($action == 'update_customer') {
    $customer_id = filter_input(INPUT_POST, 'CustomerId', FILTER_VALIDATE_INT);
    $customer = $customer_db->get_customer_info($customer_id);
    if (!$customer || !user_can_access_customer($user_info, $customer)) {
        include '../view/record_access_error.php';
        exit;
    }
    $states = $states_db->get_all_states();
    $payload = collect_customer_payload();
    $errors = validate_customer_payload($payload, $states);
    if (!empty($errors)) {
        $customer = array_merge($customer, $payload);
        include 'customer_edit.php';
        exit;
    }
    $customer_db->update_customer($customer_id, $payload);
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
        'Zip' => 'number'
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
        $data['Phone'] = '';
        $data['Email'] = '';
        $customer_db->create_customer($data);
    }

    // You could add other types here in the future
    // else if ($type === 'contact') { ... }

    header("Location: index.php?action=list_customers");
} 

?>
