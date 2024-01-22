<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
$user_id = $_SESSION['userId'];

require '../model/database.php';
require '../model/user_settings_db.php';
require '../model/users_db.php';
$user_settings_db = new UserSettingsDB;
$users_db = new UsersDB;

$action = filter_input(INPUT_POST, 'action');
if ($action == null) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == null) {
        header('Location: ../homepage.php');
    }
} 

if ($action == 'view_user_settings') {
    $user_info = $users_db->get_user_info($user_id);
    $user_settings = $user_settings_db->get_user_settings($user_id);
    include 'view_user_settings.php';
} else if ($action == 'edit_firstname') {
     $new_firstname = filter_input(INPUT_POST,'new_firstname');
     $execute = $users_db->update_firstname($new_firstname, $user_id);
     header('Location: .?action=view_user_settings');
} else if ($action == 'edit_lastname') {
    $new_lastname = filter_input(INPUT_POST,'new_lastname');
    $execute = $users_db->update_lastname($new_lastname, $user_id);
    header('Location: .?action=view_user_settings');
} else if ($action == 'change_email') {
    $new_email = filter_input(INPUT_POST,'new_email');
    $execute = $users_db->change_email($new_email, $user_id);
    header('Location: .?action=view_user_settings');
} else if ($action == 'change_password') {
    $new_password = filter_input(INPUT_POST,'new_password');
    $execute = $users_db->change_password($new_password, $user_id);
    header('Location: .?action=view_user_settings');
} else if ($action == 'edit_statement_overdue_notification') {
    if (isset($_POST['new_statement_overdue_notification'])) {
        $new_statement_overdue_notification = 'true';
    } else {
        $new_statement_overdue_notification = 'false';
    }
    $execute = $user_settings_db->update_statement_overdue_notification($new_statement_overdue_notification, $user_id);
    header('Location: .?action=view_user_settings');
} else if ($action == 'edit_statement_overdue_notification_days') {
    $new_statement_overdue_notification_days = filter_input(INPUT_POST,'new_statement_overdue_notification_days');
    $execute = $user_settings_db->update_statement_overdue_notification_days($new_statement_overdue_notification_days, $user_id);
    header('Location: .?action=view_user_settings');
}

?>