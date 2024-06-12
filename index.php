<?php 
session_start();
require 'model/database.php';
require 'model/users_db.php';
require 'model/email_server.php';
$users_db = new UsersDB;
$email_server = new EmailServer; 

$action = filter_input(INPUT_POST, 'action');
    if ($action == null) {
        $action = filter_input(INPUT_GET, 'action');
        if ($action == null) {
            $action = 'login';
        }
    }
    
if (!isset($_SESSION["logged_in"])) {

    if ($action == 'check_login') {
        $email = filter_input(INPUT_POST, 'email');
        $password = filter_input(INPUT_POST, 'password');
        $check_user = $users_db->login_user($email, $password);
        if ($check_user == false) {
            header("Location: login.php?message=Incorrect login credentials. Please try again.");
        } else {
            header("Location: homepage.php");
        }      
    } else if ($action == 'signup') {
        include 'signup.php';
    } else if ($action == 'login') {
        include 'login.php';
    } else if ($action == 'check_signup') {
        $first_name = filter_input(INPUT_POST, 'first_name'); 
        $last_name = filter_input(INPUT_POST, 'last_name');
        $email = filter_input(INPUT_POST, 'email');
        $password = filter_input(INPUT_POST, 'password');
        // validate the email address for @ sign 
        if (str_contains($email, '@')) {
            // validate the email address for '.'
            if (str_contains($email, '.')) {
                // make sure no other user has the same email address
                $check_email = $users_db->check_email($email);
                if ($check_email == false) {
                    header("Location: signup.php?message=There is already a user with that email address. Please try again.");
                } else {
                    // save new user to db
                    $create_user = $users_db->create_user($first_name, $last_name, $email, $password);
                    if ($create_user == true) {
                        header("Location: login.php?message=User account created. Please login.");
                    } else {
                        header("Location: signup.php?message=Sign up error. Please try again.");
                    }       
                }    
            } else {
                // if email does not contain '.'
                header("Location: signup.php?message=Please enter a valid email address.");
            }
        } else {
            // if email does not contain '@'
            header("Location: signup.php?message=Please enter a valid email address.");
        }
    } else if ($action == 'forgot_password') {
        include 'reset_password.php';
    } else if ($action == 'send_code') {
        $email = filter_input(INPUT_POST, 'email'); 
        $account_exists = $users_db->get_userid_from_email($email);
        if ($account_exists == false) {
            // navigate to enter code page and do nothing
            include 'enter_code.php';
        } else {
            // navigate to enter code page and send code 
            $_SESSION["userId"] = $account_exists['userId'];
            $code = mt_rand(100000, 999999);

            // get the email password 
            $email_account_type = 'internal';
            $email_password = $email_server->get_email_password($email_account_type);
            $success = $email_server->send_code($code, $email, $email_password);
            header("Location: enter_code.php?message=Code Sent");
        }
    } else if ($action == 'submit_code') {
        //logic for submitting code for check 
        $code = $_SESSION["code"];
        $entered_code = filter_input(INPUT_POST, 'entered_code'); 
        if ($code == $entered_code) {
            $_SESSION["can_change_password"] = true;
            header("Location: new_password.php");
        } else {
            header("Location: enter_code.php?message=Code Incorrect");
        }
    } else if ($action == 'submit_password') {
        $new_password = filter_input(INPUT_POST, 'new_password');
        $user_id = $_SESSION["userId"];
        $success = $users_db->change_password($user_id, $new_password);
        // destroy session
        $_SESSION = array();
        session_destroy();
        $name = session_name();
        $expire = strtotime('-1 year');
        $params = session_get_cookie_params();
        $path = $params['path'];
        $domain = $params['domain'];
        $secure = $params['secure'];
        $httponly = $params['httponly'];
        setcookie($name, '', $expire, $path, $domain, $secure, $httponly);
        header("Location: login.php?message=New Password Saved. Please Login.");
    }
} else {
    if ($action == 'logout') {
        $_SESSION = array();
        session_destroy();
        $name = session_name();
        $expire = strtotime('-1 year');
        $params = session_get_cookie_params();
        $path = $params['path'];
        $domain = $params['domain'];
        $secure = $params['secure'];
        $httponly = $params['httponly'];
        setcookie($name, '', $expire, $path, $domain, $secure, $httponly);
        header("Location: login.php");
    } else {
        header("Location: homepage.php");
    }
}
?>

