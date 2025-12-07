<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

require '../model/database.php';
require '../model/customer_db.php';
require '../model/users_db.php';
require '../model/contact_db.php';
require '../model/state_db.php';

$user_id = $_SESSION['userId'];
$customer_db = new CustomerDB;
$users_db = new UsersDB;
$contact_db = new ContactDB;
$states_db = new StateDB;

$user_info = $users_db->get_user_info($user_id);

function user_can_access_customer($user_info, $customer_info) {
    return $user_info['superuser'] == 1 || $user_info['userId'] == $customer_info['userId'];
}

function collect_contact_payload() {
    return [
        'FirstName' => trim((string) filter_input(INPUT_POST, 'FirstName')),
        'LastName' => trim((string) filter_input(INPUT_POST, 'LastName')),
        'Address1' => trim((string) filter_input(INPUT_POST, 'Address1')),
        'Address2' => trim((string) filter_input(INPUT_POST, 'Address2')),
        'City' => trim((string) filter_input(INPUT_POST, 'City')),
        'StateId' => trim((string) filter_input(INPUT_POST, 'StateId')),
        'Zip' => trim((string) filter_input(INPUT_POST, 'Zip')),
        'Phone' => trim((string) filter_input(INPUT_POST, 'Phone')),
        'Email' => trim((string) filter_input(INPUT_POST, 'Email')),
        'ReceiveStatements' => filter_input(INPUT_POST, 'ReceiveStatements') ? 1 : 0,
        'IsPrimary' => filter_input(INPUT_POST, 'IsPrimary') ? 1 : 0,
    ];
}

function validate_contact_payload($data, $states) {
    $errors = [];
    if ($data['FirstName'] === '') {
        $errors[] = 'First name is required.';
    }
    if ($data['LastName'] === '') {
        $errors[] = 'Last name is required.';
    }
    if ($data['Phone'] === '' && $data['Email'] === '') {
        $errors[] = 'Enter a phone number or an email.';
    }
    if ($data['Email'] !== '' && !filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if ($data['Phone'] !== '' && !preg_match('/^[0-9\\-\\+\\(\\)\\s]{7,20}$/', $data['Phone'])) {
        $errors[] = 'Enter a valid phone number.';
    }
    $state_ids = array_map(function ($state) {
        return $state['StateId'];
    }, $states);
    if ($data['StateId'] !== '' && !in_array($data['StateId'], $state_ids)) {
        $errors[] = 'Select a valid state.';
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

if ($action == 'customer_contacts') {
    $customer_id = filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info) {
        include '../view/record_access_error.php';
        exit;
    }
    if (!user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $contacts = $contact_db->get_contacts_by_customer($customer_id);
    include 'customer_contacts.php';
} else if ($action == 'view_contact') {
    $contact_id = filter_input(INPUT_GET, 'contact_id', FILTER_VALIDATE_INT);
    $contact = $contact_db->get_contact($contact_id);
    if (!$contact) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_info = $customer_db->get_customer_info($contact['CustomerId']);
    if (!user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    include 'contact.php';
} else if ($action == 'edit_contact') {
    $contact_id = filter_input(INPUT_GET, 'contact_id', FILTER_VALIDATE_INT);
    $contact = $contact_db->get_contact($contact_id);
    if (!$contact) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_info = $customer_db->get_customer_info($contact['CustomerId']);
    if (!user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $states = $states_db->get_all_states();
    $errors = [];
    include 'contact_edit.php';
} else if ($action == 'update_contact') {
    $contact_id = filter_input(INPUT_POST, 'ContactId', FILTER_VALIDATE_INT);
    $contact = $contact_db->get_contact($contact_id);
    if (!$contact) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_info = $customer_db->get_customer_info($contact['CustomerId']);
    if (!user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $states = $states_db->get_all_states();
    $payload = collect_contact_payload();
    $errors = validate_contact_payload($payload, $states);
    if (!empty($errors)) {
        $contact = array_merge($contact, $payload);
        include 'contact_edit.php';
        exit;
    }
    if (!empty($payload['IsPrimary'])) {
        $contact_db->clear_primary_for_customer($contact['CustomerId'], $contact_id);
    }
    $contact_db->update_contact($contact_id, $payload);
    header('Location: index.php?action=view_contact&contact_id=' . $contact_id);
} else if ($action == 'create_contact') {
    $customer_id = filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info) {
        include '../view/record_access_error.php';
        exit;
    }
    if (!user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $states = $states_db->get_all_states();
    $errors = [];
    $contact = [
        'FirstName' => '',
        'LastName' => '',
        'Address1' => '',
        'Address2' => '',
        'City' => '',
        'StateId' => '',
        'Zip' => '',
        'Phone' => '',
        'Email' => '',
        'ReceiveStatements' => 1,
        'IsPrimary' => 0,
        'CustomerId' => $customer_id,
    ];
    include 'contact_create.php';
} else if ($action == 'store_contact') {
    $customer_id = filter_input(INPUT_POST, 'CustomerId', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info) {
        include '../view/record_access_error.php';
        exit;
    }
    if (!user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $states = $states_db->get_all_states();
    $payload = collect_contact_payload();
    $payload['CustomerId'] = $customer_id;
    $errors = validate_contact_payload($payload, $states);
    if (!empty($errors)) {
        $contact = $payload;
        include 'contact_create.php';
        exit;
    }
    if (!empty($payload['IsPrimary'])) {
        $contact_db->clear_primary_for_customer($customer_id);
    }
    $new_id = $contact_db->create_contact($payload);
    header('Location: index.php?action=view_contact&contact_id=' . $new_id);
} else if ($action == 'delete_contact') {
    $contact_id = filter_input(INPUT_POST, 'contact_id', FILTER_VALIDATE_INT);
    $contact = $contact_db->get_contact($contact_id);
    if (!$contact) {
        include '../view/record_access_error.php';
        exit;
    }
    $customer_info = $customer_db->get_customer_info($contact['CustomerId']);
    if (!user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $contact_db->delete_contact($contact_id);
    header('Location: index.php?action=customer_contacts&customer_id=' . $contact['CustomerId']);
} else if ($action == 'delete_selected_contacts') {
    $customer_id = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
    $customer_info = $customer_db->get_customer_info($customer_id);
    if (!$customer_info) {
        include '../view/record_access_error.php';
        exit;
    }
    if (!user_can_access_customer($user_info, $customer_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $selected_ids = $_POST['selected_contacts'] ?? [];
    $contact_db->delete_multiple_contacts($selected_ids);
    header('Location: index.php?action=customer_contacts&customer_id=' . $customer_id);
}

?>
