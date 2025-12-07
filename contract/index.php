<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
$user_id = $_SESSION['userId'];
require '../model/database.php';
require '../model/users_db.php';
require '../model/contracts_db.php';
require '../model/customer_db.php';
require '../model/contract_term_db.php';
require '../model/payment_methods_db.php';
$contracts_db = new ContractsDB;
$users_db = new UsersDB;
$customer_db = new CustomerDB;
$contract_term_db = new ContractTermDB;
$payment_methods_db = new PaymentMethodsDB;

$user_info = $users_db->get_user_info($user_id);
$action = filter_input(INPUT_POST, 'action');
if ($action == null) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == null) {
        header('Location: ../homepage.php');
    }
}

function user_can_access_customer($user_info, $customer_info) {
    return $user_info['superuser'] == 1 || $user_info['userId'] == $customer_info['userId'];
}

function collect_contract_payload($customer_id_override = null) {
    $customer_id = $customer_id_override ?? filter_input(INPUT_POST, 'CustomerId', FILTER_VALIDATE_INT);
    return [
        'Name' => trim((string) filter_input(INPUT_POST, 'Name')),
        'PropertyId' => filter_input(INPUT_POST, 'PropertyId', FILTER_VALIDATE_INT) ?? 0,
        'CustomerId' => $customer_id ?? 0,
        'CompanyId' => filter_input(INPUT_POST, 'CompanyId', FILTER_VALIDATE_INT) ?? 0,
        'BaseAmt' => trim((string) filter_input(INPUT_POST, 'BaseAmt')),
        'CAM' => trim((string) filter_input(INPUT_POST, 'CAM')),
        'BillingCycleStart' => filter_input(INPUT_POST, 'BillingCycleStart', FILTER_VALIDATE_INT) ?? 0,
        'BillingCycleEnd' => trim((string) filter_input(INPUT_POST, 'BillingCycleEnd')),
        'DueDate' => trim((string) filter_input(INPUT_POST, 'DueDate')),
        'LateDate' => filter_input(INPUT_POST, 'LateDate', FILTER_VALIDATE_INT) ?? 0,
        'LateFee' => trim((string) filter_input(INPUT_POST, 'LateFee')),
        'StatementSendDate' => filter_input(INPUT_POST, 'StatementSendDate', FILTER_VALIDATE_INT) ?? 0,
        'NumPaymentsDue' => filter_input(INPUT_POST, 'NumPaymentsDue', FILTER_VALIDATE_INT) ?? 0,
        'TotalPaymentsDue' => filter_input(INPUT_POST, 'TotalPaymentsDue', FILTER_VALIDATE_INT) ?? 0,
        'ContractType' => trim((string) filter_input(INPUT_POST, 'ContractType')),
        'TestContract' => isset($_POST['TestContract']) ? 1 : 0,
        'StatementAutoReceive' => isset($_POST['StatementAutoReceive']) ? 'true' : 'false',
        'StripePaymentMethodId' => filter_input(INPUT_POST, 'StripePaymentMethodId', FILTER_VALIDATE_INT) ?? 0,
    ];
}

function validate_contract_payload($data) {
    $errors = [];
    if ($data['Name'] === '') {
        $errors[] = 'Name is required.';
    }
    if ($data['CustomerId'] <= 0) {
        $errors[] = 'Customer is required.';
    }
    if ($data['ContractType'] === '') {
        $errors[] = 'Contract type is required.';
    }
    foreach (['PropertyId', 'CompanyId', 'BillingCycleStart', 'LateDate', 'StatementSendDate', 'NumPaymentsDue', 'TotalPaymentsDue', 'StripePaymentMethodId'] as $intField) {
        if ($data[$intField] !== null && $data[$intField] !== '' && !is_numeric($data[$intField])) {
            $errors[] = ucfirst($intField) . ' must be a number.';
        }
    }
    foreach (['BaseAmt', 'CAM', 'LateFee'] as $numField) {
        if ($data[$numField] !== '' && !is_numeric($data[$numField])) {
            $errors[] = ucfirst($numField) . ' must be numeric.';
        }
    }
    if ($data['DueDate'] !== '' && !preg_match('/^[0-9]{1,2}$/', $data['DueDate'])) {
        $errors[] = 'Due date should be a day of month (number).';
    }
    if ($data['BillingCycleEnd'] !== '' && strlen($data['BillingCycleEnd']) > 10) {
        $errors[] = 'Billing cycle end is too long.';
    }
    return $errors;
}

if ($action == 'view_contract') {
    $contract_id = filter_input(INPUT_GET, 'contract_id', FILTER_VALIDATE_INT);
    $contract_info = $contracts_db->get_contract_info($contract_id); 
    if (!$contract_info) {
        include '../view/record_access_error.php';
        exit;
    }
    $current_term = $contract_term_db->get_current_term($contract_id);
    $contract_terms = $contract_term_db->get_all_terms($contract_id);
    $customer_id = $contract_info['CustomerId'];
    $customer_payment_methods = $payment_methods_db->get_payment_methods($customer_id);
    if (user_can_access_customer($user_info, $contract_info)) {
        include 'view_contract.php';
    } else {
        include '../view/record_access_error.php';
    }
} else if ($action == 'view_contracts_list') {
    $customer_id = filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info) {
        include '../view/record_access_error.php';
        exit;
    }
    $contracts = $contracts_db->get_contracts_by_customer($customer_id);
    if (user_can_access_customer($user_info, $customer_info)) {
        include 'view_contracts_list.php';
    } else {
        include '../view/record_access_error.php';
    }
} else if ($action == 'create_contract') {
    $customer_id = filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info || !user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_payment_methods = $payment_methods_db->get_payment_methods($customer_id);
    $contract = [
        'Name' => '',
        'PropertyId' => 0,
        'CustomerId' => $customer_id,
        'CompanyId' => 0,
        'BaseAmt' => '',
        'CAM' => '',
        'BillingCycleStart' => '',
        'BillingCycleEnd' => '',
        'DueDate' => '',
        'LateDate' => '',
        'LateFee' => '',
        'StatementSendDate' => '',
        'NumPaymentsDue' => '',
        'TotalPaymentsDue' => '',
        'ContractType' => '',
        'TestContract' => 0,
        'StatementAutoReceive' => 'false',
        'StripePaymentMethodId' => 0,
    ];
    $errors = [];
    include 'contract_create.php';
} else if ($action == 'store_contract') {
    $customer_id = filter_input(INPUT_POST, 'CustomerId', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info || !user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_payment_methods = $payment_methods_db->get_payment_methods($customer_id);
    $payload = collect_contract_payload($customer_id);
    $errors = validate_contract_payload($payload);
    if (!empty($errors)) {
        $contract = $payload;
        include 'contract_create.php';
        exit;
    }
    if ($payload['StripePaymentMethodId'] > 0) {
        $payload['StatementAutoReceive'] = 'false';
    }
    $new_id = $contracts_db->create_contract($payload);
    header('Location: .?action=view_contract&contract_id=' . $new_id);
} else if ($action == 'edit_contract') {
    $contract_id = filter_input(INPUT_GET, 'contract_id', FILTER_VALIDATE_INT);
    $contract_info = $contracts_db->get_contract_info($contract_id);
    if (!$contract_info || !user_can_access_customer($user_info, $contract_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_id = $contract_info['CustomerId'];
    $customer_info = $customer_db->get_customer_info($customer_id);
    $customer_payment_methods = $payment_methods_db->get_payment_methods($customer_id);
    $contract = $contract_info;
    $errors = [];
    include 'contract_edit.php';
} else if ($action == 'update_contract') {
    $contract_id = filter_input(INPUT_POST, 'ContractId', FILTER_VALIDATE_INT);
    $contract_info = $contracts_db->get_contract_info($contract_id);
    if (!$contract_info || !user_can_access_customer($user_info, $contract_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_id = $contract_info['CustomerId'];
    $customer_info = $customer_db->get_customer_info($customer_id);
    $customer_payment_methods = $payment_methods_db->get_payment_methods($customer_id);
    $payload = collect_contract_payload($customer_id);
    $errors = validate_contract_payload($payload);
    if (!empty($errors)) {
        $contract = array_merge($contract_info, $payload);
        include 'contract_edit.php';
        exit;
    }
    if ($payload['StripePaymentMethodId'] > 0) {
        $payload['StatementAutoReceive'] = 'false';
    }
    $contracts_db->update_contract_fields($contract_id, $payload);
    header('Location: .?action=view_contract&contract_id=' . $contract_id);
} else if ($action == 'delete_contract') {
    $contract_id = filter_input(INPUT_POST, 'contract_id', FILTER_VALIDATE_INT);
    $contract_info = $contracts_db->get_contract_info($contract_id);
    if (!$contract_info || !user_can_access_customer($user_info, $contract_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $contracts_db->delete_contract($contract_id);
    header('Location: .?action=view_contracts_list&customer_id=' . $contract_info['CustomerId']);
} else if ($action == 'delete_selected_contracts') {
    $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info || !user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $selected_ids = $_POST['selected_contracts'] ?? [];
    $contracts_db->delete_multiple_contracts($selected_ids);
    header('Location: .?action=view_contracts_list&customer_id=' . $customer_id);
} else if ($action == 'edit_statementautoreceive') {
    $contract_id = filter_input(INPUT_POST, 'contract_id', FILTER_VALIDATE_INT);
    if (isset($_POST['new_statementautoreceive'])) {
        $col_val = 'true';
    } else {
        $col_val = 'false';
    }
    $contracts_db->update_statementautoreceive($contract_id, $col_val);
    header('Location: .?action=view_contract&contract_id=' . $contract_id); 
} else if ($action == 'update_contract_payment_method') {
    $contract_id = filter_input(INPUT_POST, 'contract_id', FILTER_VALIDATE_INT);
    $payment_method_id = filter_input(INPUT_POST, 'payment_method_id', FILTER_VALIDATE_INT);
    if (empty($payment_method_id)) {
        $payment_method_id = 0;
    }
    $contracts_db->update_payment_method($contract_id, $payment_method_id);
    if ($payment_method_id > 0) {
        $contracts_db->update_statementautoreceive($contract_id, 'false');
    }
    header('Location: .?action=view_contract&contract_id=' . $contract_id);
}

?>
