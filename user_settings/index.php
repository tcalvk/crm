<?php 
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
}
$user_id = $_SESSION['userId'];

require '../model/database.php';
require '../model/user_settings_db.php';
$user_settings_db = new UserSettingsDB;

$action = filter_input(INPUT_POST, 'action');
if ($action == null) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == null) {
        header('Location: ../homepage.php');
    }
} 

if ($action == 'view_user_settings') {
    $user_settings = $user_settings_db->get_user_settings($user_id);
    include 'view_user_settings.php';
}

?>