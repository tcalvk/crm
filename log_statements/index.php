<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

require '../model/database.php';
require '../model/log_statements_db.php';
require '../model/log_fixed_payments_db.php';
require '../model/contracts_db.php';
require '../model/customer_db.php';
require '../model/users_db.php';

$user_id = $_SESSION['userId'];
$log_statements_db = new LogStatementsDB;
$log_fixed_payments_db = new LogFixedPaymentsDB;
$contracts_db = new ContractsDB;
$customer_db = new CustomerDB;
$users_db = new UsersDB;
$user_info = $users_db->get_user_info($user_id);

function user_can_access_customer($user_info, $customer_info) {
    return $user_info['superuser'] == 1 || $user_info['userId'] == $customer_info['userId'];
}

function collect_statement_payload() {
    return [
        'ContractId' => filter_input(INPUT_POST, 'ContractId', FILTER_VALIDATE_INT),
        'DueDate' => trim((string) filter_input(INPUT_POST, 'DueDate')),
        'CreatedDate' => trim((string) filter_input(INPUT_POST, 'CreatedDate')),
        'BaseAmt' => trim((string) filter_input(INPUT_POST, 'BaseAmt')),
        'CAM' => trim((string) filter_input(INPUT_POST, 'CAM')),
        'PaymentNumber' => trim((string) filter_input(INPUT_POST, 'PaymentNumber')),
    ];
}

function validate_statement_payload($payload, $contracts) {
    $errors = [];
    $valid_contract_ids = array_map(function ($contract) {
        return (int) $contract['ContractId'];
    }, $contracts);

    if (!$payload['ContractId'] || !in_array((int) $payload['ContractId'], $valid_contract_ids)) {
        $errors[] = 'Select a valid contract.';
    }

    if ($payload['DueDate'] === '' || !DateTime::createFromFormat('Y-m-d', $payload['DueDate'])) {
        $errors[] = 'Enter a valid due date.';
    }

    if ($payload['CreatedDate'] === '' || !DateTime::createFromFormat('Y-m-d', $payload['CreatedDate'])) {
        $errors[] = 'Enter a valid created date.';
    }

    if ($payload['BaseAmt'] === '' || !is_numeric($payload['BaseAmt'])) {
        $errors[] = 'Enter a base amount.';
    }

    if ($payload['CAM'] === '' || !is_numeric($payload['CAM'])) {
        $errors[] = 'Enter a CAM amount (0 if not applicable).';
    }

    if ($payload['PaymentNumber'] !== '' && !ctype_digit($payload['PaymentNumber'])) {
        $errors[] = 'Payment number must be a whole number.';
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

if ($action == 'view_statement') {
    $statement_number = filter_input(INPUT_GET, 'statement_number', FILTER_VALIDATE_INT);
    if (!$statement_number) {
        include '../view/record_access_error.php';
        exit;
    }
    $statement = $log_statements_db->get_statement($statement_number);
    if (!$statement) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_info = $customer_db->get_customer_info($statement['CustomerId']);
    if (!$customer_info || !user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    if ($statement['WrittenOff'] > 0) {
        $display = 0;
    } else {
        $display = 1;
    }
    $customer_id = $customer_info['CustomerId'];
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
    $customer_id = filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info || !user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_id = $customer_info['CustomerId'];
    $statements = $log_statements_db->get_statements($customer_id);
    include 'view_statements_list.php';
} else if ($action == 'mark_paid') {
    $statement_number = filter_input(INPUT_POST, 'statement_number', FILTER_VALIDATE_INT);
    $paid_date = filter_input(INPUT_POST, 'paid_date');
    $payment_amount = filter_input(INPUT_POST, 'payment_amount');
    $statement = $log_statements_db->get_statement($statement_number);
    if (!$statement) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_info = $customer_db->get_customer_info($statement['CustomerId']);
    if (!$customer_info || !user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
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
    header('Location: .?action=view_statement&statement_number=' . $statement_number);
} else if ($action == 'clear_paid_date') {
    $statement_number = filter_input(INPUT_POST, 'statement_number', FILTER_VALIDATE_INT);
    $statement = $log_statements_db->get_statement($statement_number);
    if (!$statement) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_info = $customer_db->get_customer_info($statement['CustomerId']);
    if (!$customer_info || !user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
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
    header('Location: .?action=view_statement&statement_number=' . $statement_number);
} else if ($action == 'write_off') {
    $statement_number = filter_input(INPUT_POST, 'statement_number', FILTER_VALIDATE_INT);
    $statement = $log_statements_db->get_statement($statement_number);
    if (!$statement) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_info = $customer_db->get_customer_info($statement['CustomerId']);
    if (!$customer_info || !user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $success = $log_statements_db->write_off_statement($statement_number);
    $success2 = $log_statements_db->clear_paid_date($statement_number);
    header('Location: .?action=view_statement&statement_number=' . $statement_number);
} else if ($action == 'delete_statement') {
    $statement_number = filter_input(INPUT_POST, 'statement_number', FILTER_VALIDATE_INT);
    $statement = $log_statements_db->get_statement($statement_number);
    if (!$statement) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_info = $customer_db->get_customer_info($statement['CustomerId']);
    if (!$customer_info || !user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $log_statements_db->delete_statement($statement_number);
    header('Location: .?action=view_all&customer_id=' . $customer_info['CustomerId']);
} else if ($action == 'create_statement') {
    if ($user_info['superuser'] != 1) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_id = filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info) {
        include '../view/record_access_error.php';
        exit;
    }
    $contracts = $contracts_db->get_contracts($customer_id);
    $errors = [];
    $statement_data = [
        'CreatedDate' => date('Y-m-d'),
        'DueDate' => '',
        'BaseAmt' => '',
        'CAM' => '',
        'PaymentNumber' => '',
        'ContractId' => '',
    ];
    include 'create_statement.php';
} else if ($action == 'store_statement') {
    if ($user_info['superuser'] != 1) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_id = filter_input(INPUT_POST, 'CustomerId', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info) {
        include '../view/record_access_error.php';
        exit;
    }
    $contracts = $contracts_db->get_contracts($customer_id);
    $statement_data = collect_statement_payload();
    $errors = validate_statement_payload($statement_data, $contracts);

    if (!empty($errors)) {
        include 'create_statement.php';
        exit;
    }

    $total_amt = (float) $statement_data['BaseAmt'] + (float) $statement_data['CAM'];
    $statement_number = $log_statements_db->generate_statement_number();

    $log_statements_db->create_manual_statement([
        'StatementNumber' => $statement_number,
        'CreatedDate' => $statement_data['CreatedDate'],
        'TotalAmt' => $total_amt,
        'ContractId' => $statement_data['ContractId'],
        'DueDate' => $statement_data['DueDate'],
        'BaseAmt' => $statement_data['BaseAmt'],
        'CAM' => $statement_data['CAM'],
        'PaymentNumber' => $statement_data['PaymentNumber'],
    ]);

    header('Location: .?action=view_statement&statement_number=' . $statement_number);
}

?>
