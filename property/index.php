<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}

require '../model/database.php';
require '../model/users_db.php';
require '../model/property_db.php';
require '../model/state_db.php';

$user_id = $_SESSION['userId'];
$users_db = new UsersDB;
$property_db = new PropertyDB;
$state_db = new StateDB;

$user_info = $users_db->get_user_info($user_id);

function user_can_access_property($user_info, $property_info) {
    return $user_info['superuser'] == 1 || $user_info['userId'] == $property_info['OwnedBy'];
}

function collect_property_payload($user_id) {
    return [
        'Name' => trim((string) filter_input(INPUT_POST, 'Name')),
        'Address1' => trim((string) filter_input(INPUT_POST, 'Address1')),
        'Address2' => trim((string) filter_input(INPUT_POST, 'Address2')),
        'Address3' => trim((string) filter_input(INPUT_POST, 'Address3')),
        'City' => trim((string) filter_input(INPUT_POST, 'City')),
        'StateId' => trim((string) filter_input(INPUT_POST, 'StateId')),
        'Zip' => trim((string) filter_input(INPUT_POST, 'Zip')),
        'OwnedBy' => $user_id,
    ];
}

function validate_property_payload($data, $states) {
    $errors = [];
    if ($data['Name'] === '') {
        $errors[] = 'Property name is required.';
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

if ($action == 'list_properties') {
    if ($user_info['superuser'] == 1) {
        $properties = $property_db->get_properties_sudo();
    } else {
        $properties = $property_db->get_properties($user_id);
    }
    include 'view_property_list.php';
} else if ($action == 'view_property') {
    $property_id = filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
    $property_info = $property_db->get_property($property_id);
    if (!$property_info || !user_can_access_property($user_info, $property_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    include 'view_property.php';
} else if ($action == 'create_property') {
    $states = $state_db->get_all_states();
    $property = [
        'Name' => '',
        'Address1' => '',
        'Address2' => '',
        'Address3' => '',
        'City' => '',
        'StateId' => '',
        'Zip' => '',
    ];
    $errors = [];
    include 'property_create.php';
} else if ($action == 'store_property') {
    $states = $state_db->get_all_states();
    $payload = collect_property_payload($user_id);
    $errors = validate_property_payload($payload, $states);
    if (!empty($errors)) {
        $property = $payload;
        include 'property_create.php';
        exit;
    }
    $new_id = $property_db->create_property($payload);
    header('Location: .?action=view_property&property_id=' . $new_id);
} else if ($action == 'delete_property') {
    $property_id = filter_input(INPUT_POST, 'property_id', FILTER_VALIDATE_INT);
    $property_info = $property_db->get_property($property_id);
    if (!$property_info || !user_can_access_property($user_info, $property_info)) {
        include '../view/record_access_error.php';
        exit;
    }
    $property_db->delete_property($property_id);
    header('Location: .?action=list_properties');
} else if ($action == 'delete_selected_properties') {
    $selected_ids = $_POST['selected_properties'] ?? [];
    foreach ($selected_ids as $pid) {
        $property_info = $property_db->get_property((int) $pid);
        if ($property_info && user_can_access_property($user_info, $property_info)) {
            $property_db->delete_property((int) $pid);
        }
    }
    header('Location: .?action=list_properties');
}
?>
