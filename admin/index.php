<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("Location: ../login.php");
}

$user_id = $_SESSION['userId'];

require '../model/database.php';
require '../model/users_db.php';
require '../model/user_invites_db.php';

$users_db = new UsersDB;
$user_invites_db = new UserInvitesDB;
$user_info = $users_db->get_user_info($user_id);

if (empty($user_info['superuser']) || $user_info['superuser'] != 1) {
    include '../view/record_access_error.php';
    exit;
}

$action = filter_input(INPUT_POST, 'action');
if ($action == null) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == null) {
        $action = 'view_admin_settings';
    }
}

$per_page = 25;
$page = max(1, (int) filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT));
$user_search = trim((string) filter_input(INPUT_GET, 'search', FILTER_UNSAFE_RAW));
$invites_per_page = 25;
$invites_page = max(1, (int) filter_input(INPUT_GET, 'invite_page', FILTER_VALIDATE_INT));
$invite_search = trim((string) filter_input(INPUT_GET, 'invite_search', FILTER_UNSAFE_RAW));

if ($action == 'view_admin_settings') {
    if (!empty($user_search)) {
        $total_users = $users_db->get_user_count_search($user_search);
        $total_pages = max(1, (int) ceil($total_users / $per_page));
        if ($page > $total_pages) {
            $page = $total_pages;
        }
        $offset = ($page - 1) * $per_page;
        $users = $users_db->get_users_paginated_search($per_page, $offset, $user_search);
    } else {
        $total_users = $users_db->get_user_count();
        $total_pages = max(1, (int) ceil($total_users / $per_page));
        if ($page > $total_pages) {
            $page = $total_pages;
        }
        $offset = ($page - 1) * $per_page;
        $users = $users_db->get_users_paginated($per_page, $offset);
    }

    if (!empty($invite_search)) {
        $total_invites = $user_invites_db->get_invite_count_search($invite_search);
        $invite_total_pages = max(1, (int) ceil($total_invites / $invites_per_page));
        if ($invites_page > $invite_total_pages) {
            $invites_page = $invite_total_pages;
        }
        $invite_offset = ($invites_page - 1) * $invites_per_page;
        $invites = $user_invites_db->get_invites_paginated_search($invites_per_page, $invite_offset, $invite_search);
    } else {
        $total_invites = $user_invites_db->get_invite_count();
        $invite_total_pages = max(1, (int) ceil($total_invites / $invites_per_page));
        if ($invites_page > $invite_total_pages) {
            $invites_page = $invite_total_pages;
        }
        $invite_offset = ($invites_page - 1) * $invites_per_page;
        $invites = $user_invites_db->get_invites_paginated($invites_per_page, $invite_offset);
    }
    include 'view_admin_settings.php';
} else if ($action == 'edit_superuser') {
    $target_user_id = filter_input(INPUT_POST, 'target_user_id', FILTER_VALIDATE_INT);
    $new_superuser = filter_input(INPUT_POST, 'new_superuser', FILTER_VALIDATE_INT);
    if ($target_user_id !== null && $target_user_id !== false && ($new_superuser === 0 || $new_superuser === 1)) {
        $users_db->update_superuser_status($target_user_id, $new_superuser);
    }
    header('Location: .?action=view_admin_settings');
} else if ($action == 'delete_user') {
    $target_user_id = filter_input(INPUT_POST, 'target_user_id', FILTER_VALIDATE_INT);
    if ($target_user_id !== null && $target_user_id !== false) {
        $users_db->delete_user($target_user_id);
    }
    header('Location: .?action=view_admin_settings');
} else if ($action == 'delete_users_bulk') {
    $selected_user_ids = filter_input(INPUT_POST, 'selected_user_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!empty($selected_user_ids)) {
        foreach ($selected_user_ids as $id) {
            $valid_id = filter_var($id, FILTER_VALIDATE_INT);
            if ($valid_id !== false && $valid_id !== null) {
                $users_db->delete_user($valid_id);
            }
        }
    }
    header('Location: .?action=view_admin_settings');
} else if ($action == 'create_user') {
    // Placeholder: return to admin page without creating a user yet.
    header('Location: .?action=view_admin_settings');
}
?>
