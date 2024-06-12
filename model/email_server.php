<?php 
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


class EmailServer {
    public function get_email_password($email_account_type) {
        
        // App Passwords // 
        $corsaire_tech_app_password = 'dicdqnqzyhduwgkv';
        $account_services_app_password = 'cjjinlfwnijbnukf';
        ///////////////////

        if ($email_account_type == 'internal') {
            return $corsaire_tech_app_password; 
        } else {
            return $account_services_app_password; 
        }
    }

    public function send_statement($email_recipients, $property_id, $date_file, $date_formatted, $company_name, $email_password) {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                 
        $mail->isSMTP();                                   
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                  
        $mail->Username   = 'services.ondeckgroup@gmail.com';                    
        $mail->Password   = $email_password;                         
        $mail->SMTPSecure = 'tls';         
        $mail->Port       = 587;   
        $mail->SMTPDebug = 1;
        $mail->setFrom('services.ondeckgroup@gmail.com', 'Account Services');

        //Recipients
        foreach ($email_recipients as $email_recipient) : 
            $mail->addAddress($email_recipient);    
        endforeach;   
    
        //Content
        $mail->isHTML(true);                             
        $mail->Subject = 'Statement Attached';
        $body = 'Please see the attached statement dated ' . $date_formatted . '. If you have any questions, please 
        email us at toddcalvinklein@gmail.com. <br><br>';
        $body .= "Best Regards, <br><br>";
        $body .= $company_name . "<br><br>";
        $body .= "DO NOT REPLY DIRECTLY TO THIS EMAIL. FOR SERVICE, PLEASE INSTEAD EMAIL US AT TODDCALVINKLEIN@GMAIL.COM. <br><br>"; 
        $mail->Body    = $body;
        $mail->AltBody = 'Please see the attached statement.';

        $mail->addAttachment("statements/" . "property" . $property_id . "_" . $date_file . ".pdf");
    
        $mail->send();

        return true;
    }
    public function send_code($code, $email, $email_password) {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                 
        $mail->isSMTP();                                   
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                  
        $mail->Username   = 'corsaire.tech@gmail.com';                    
        $mail->Password   = $email_password;                             
        $mail->SMTPSecure = 'tls';         
        $mail->Port       = 587;   
        $mail->SMTPDebug = 0;
        $mail->setFrom('corsaire.tech@gmail.com', 'Corsaire Tech Account Services');
 
        //Recipients
        $mail->addAddress($email);    
    
        //Content
        $mail->isHTML(true);                             
        $mail->Subject = 'Corsaire CRM Verification Code';
        $mail->Body    = 'Here is your requested verification code: ' . $code;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
        $mail->send();

        $_SESSION["code"] = $code;
        return true;
    }
    public function send_statement_test($email_recipients, $property_id, $date_file, $date_formatted, $company_name, $email_password) {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                 
        $mail->isSMTP();                                   
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                  
        $mail->Username   = 'services.ondeckgroup@gmail.com';                    
        $mail->Password   = $email_password;                            
        $mail->SMTPSecure = 'tls';         
        $mail->Port       = 587;   
        $mail->SMTPDebug = 1;
        $mail->setFrom('services.ondeckgroup@gmail.com', 'Account Services');

        //Recipients
        foreach ($email_recipients as $email_recipient) : 
            $mail->addAddress($email_recipient);    
        endforeach;
        
        //Content
        $mail->isHTML(true);                             
        $mail->Subject = 'Statement Attached';
        $body = 'Please see the attached statement dated ' . $date_formatted . '. If you have any questions, please 
        email us at toddcalvinklein@gmail.com. <br><br>';
        $body .= "Best Regards, <br><br>";
        $body .= $company_name . "<br><br>";
        $body .= "DO NOT REPLY DIRECTLY TO THIS EMAIL. FOR SERVICE, PLEASE INSTEAD EMAIL US AT TODDCALVINKLEIN@GMAIL.COM. <br><br>"; 
        $mail->Body    = $body;
        $mail->AltBody = 'Please see the attached statement.';

        $mail->addAttachment("statements/" . "property" . $property_id . "_" . $date_file . ".pdf");
    
        $mail->send();

        return true;
    }
    public function statements_sent_notification($contract_owner_email, $email_password) {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                 
        $mail->isSMTP();                                   
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                    
        $mail->Username   = 'corsaire.tech@gmail.com';                    
        $mail->Password = $email_password;                           
        $mail->SMTPSecure = 'tls';         
        $mail->Port       = 587;   
        $mail->SMTPDebug = 1;
        $mail->setFrom('corsaire.tech@gmail.com', 'Corsaire Tech Account Services');

        //Recipient
        $mail->addAddress($contract_owner_email);    
        
        //Content
        $mail->isHTML(true);                             
        $mail->Subject = 'Customer Statements Sent';
        $body = 'This is a courtesy email from Corsaire Tech letting you know that statements were sent for your customer contracts. To view your unpaid statements, visit www.corsairetech.com.
        <br><br>';
        $body .= "Best Regards, <br><br>";
        $body .= "Corsaire Tech";
        $mail->Body    = $body;
        $mail->AltBody = 'Your statements were sent.';
    
        $mail->send();

        return true;
    }
    public function statement_overdue_notification($user_email, $statement_number, $customer_name, $contract_name, $created_date, $due_date, $email_password) {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                 
        $mail->isSMTP();                                   
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                    
        $mail->Username   = 'corsaire.tech@gmail.com';                    
        $mail->Password = $email_password;                           
        $mail->SMTPSecure = 'tls';         
        $mail->Port       = 587;   
        $mail->SMTPDebug = 1;
        $mail->setFrom('corsaire.tech@gmail.com', 'Corsaire Tech Account Services');

        //Recipient
        $mail->addAddress($user_email);    
        
        //Content
        $mail->isHTML(true);                             
        $mail->Subject = 'Statement Overdue Notification';
        $body = 'This is a courtesy email from Corsaire Tech letting you know that you have an overdue statement that may need to be addressed. See the details below: 
        <br><br>';
        $body .= 'Statement Number: '. $statement_number . '<br>';
        $body .= 'Customer Name: '. $customer_name . '<br>';
        $body .= 'Contract Name: '. $contract_name . '<br>';
        $body .= 'Statement Sent: '. $created_date . '<br>';
        $body .= 'Statement Due Date: '. $due_date . '<br><br>';
        $body .= 'You can view your overdue statements at http://corsairetech.com <br><br>';
        $body .= "Best Regards, <br><br>";
        $body .= "Corsaire Tech";
        $mail->Body    = $body;
        $mail->AltBody = 'You have overdue statements.';
    
        $mail->send();

        return true;
    }
    public function statement_auto_received($email_address, $statement_number, $contract_name, $customer_name, $created_date, $email_password) {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                 
        $mail->isSMTP();                                   
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                    
        $mail->Username   = 'corsaire.tech@gmail.com';                    
        $mail->Password = $email_password;                           
        $mail->SMTPSecure = 'tls';         
        $mail->Port       = 587;   
        $mail->SMTPDebug = 1;
        $mail->setFrom('corsaire.tech@gmail.com', 'Corsaire Tech Account Services');

        //Recipient
        $mail->addAddress($email_address);    
        
        //Content
        $mail->isHTML(true);                             
        $mail->Subject = 'Statement Auto Received';
        $body = 'This is a courtesy email from Corsaire Tech letting you know that your statement was automatically received. See the details below: 
        <br><br>';
        $body .= 'Statement Number: '. $statement_number . '<br>';
        $body .= 'Customer Name: '. $customer_name . '<br>';
        $body .= 'Contract Name: '. $contract_name . '<br>';
        $body .= 'Statement Sent: '. $created_date . '<br>';
        $body .= 'You can view and edit your statements at http://corsairetech.com <br><br>';
        $body .= "Best Regards, <br><br>";
        $body .= "Corsaire Tech";
        $mail->Body    = $body;
        $mail->AltBody = 'Your statement was auto received.';
    
        $mail->send();

        return true;
    }
}

?>