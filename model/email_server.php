<?php 
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

class EmailServer {

    public function send_statement($email, $property_id, $date_file, $date_formatted, $company_name) {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                 
        $mail->isSMTP();                                   
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                  
        $mail->Username   = 'services.ondeckgroup@gmail.com';                    
        $mail->Password   = 'lvwofpckdkzwupxz';                              
        $mail->SMTPSecure = 'tls';         
        $mail->Port       = 587;   
        $mail->SMTPDebug = 1;
        $mail->setFrom('services.ondeckgroup@gmail.com', 'Account Services');

        //Recipients
        $mail->addAddress($email);    
    
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
}




?>