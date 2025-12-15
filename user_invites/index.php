<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: ../login.php");
}

$user_id = $_SESSION['userId'];

require '../model/database.php';
require '../model/users_db.php';
require '../model/user_invites_db.php';
require '../model/email_server.php';

$users_db = new UsersDB;
$user_invites_db = new UserInvitesDB;
$email_server = new EmailServer;

$user_info = $users_db->get_user_info($user_id);
if (empty($user_info['superuser']) || $user_info['superuser'] != 1) {
    include '../view/record_access_error.php';
    exit;
}

$action = filter_input(INPUT_POST, 'action');
if ($action == null) {
    $action = filter_input(INPUT_GET, 'action');
}

if ($action === 'create_user_invite') {
    $email = strtolower(trim(filter_input(INPUT_POST, 'new_user_email', FILTER_SANITIZE_EMAIL)));
    $errors = [];

    if (empty($email)) {
        $errors[] = 'Email is required.';
    } else {
        // Basic formatting check: must contain @ and .
        $has_at = strpos($email, '@') !== false;
        $has_dot = strpos($email, '.') !== false;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$has_at || !$has_dot) {
            $errors[] = 'Email is not formatted properly.';
        }
    }

    if (empty($errors)) {
        // Check if user already exists
        $email_available = $users_db->check_email($email);
        if (!$email_available) {
            $errors[] = 'A user with that email already exists.';
        }
    }

    if (empty($errors)) {
        // Generate unique invite code
        $invite_code = '';
        do {
            if (function_exists('random_bytes')) {
                $invite_code = bin2hex(random_bytes(8));
            } else {
                $invite_code = uniqid();
            }
        } while ($user_invites_db->invite_code_exists($invite_code));

        $user_invites_db->create_invite($email, $invite_code, 'Pending');
        // Send invite email
        $email_password = $email_server->get_email_password('internal');
        $link = 'https://corsairetech.com';
        $email_server->send_user_invite($email, $invite_code, $link, $email_password);
        $_SESSION['admin_message'] = 'Invite created for ' . $email;
    } else {
        $_SESSION['admin_error'] = implode(' ', $errors);
    }

    header('Location: ../admin/index.php?action=view_admin_settings');
    exit;
} else {
    header('Location: ../admin/index.php?action=view_admin_settings');
    exit;
}
?>
