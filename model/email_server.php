<?php 
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$phpmailer_base = __DIR__ . '/../PHPMailer/src/';
require $phpmailer_base . 'Exception.php';
require $phpmailer_base . 'PHPMailer.php';
require $phpmailer_base . 'SMTP.php';


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
    public function send_payment_invite($email, $code, $link, $email_password, $user_full_name, $company_name) {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'corsaire.tech@gmail.com';
        $mail->Password   = $email_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->SMTPDebug  = 0;
        $mail->setFrom('corsaire.tech@gmail.com', 'Corsaire Tech');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Set up your payment method';
        $body = <<<HTML
<!doctype html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Connect Bank Account</title>
  </head>
  <body style="margin:0; padding:0; background-color:#f5f5f5;">
    <!-- Full-width wrapper -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f5f5f5; padding:24px 0;">
      <tr>
        <td align="center">
          <!-- Main card -->
          <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px; background-color:#ffffff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06);">
            <tr>
              <td style="padding:32px 24px 16px 24px; text-align:center; font-family:system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color:#111827;">
                <!-- Heading -->
                <h1 style="margin:0 0 16px 0; font-size:22px; font-weight:600; line-height:1.3;">
                  {User Name} from {Company Name}
                </h1>

                <!-- Intro text -->
                <p style="margin:0 0 24px 0; font-size:15px; line-height:1.6; color:#4b5563;">
                  is inviting you to add your banking information for automatic billing.
                </p>

                <!-- Access code label -->
                <p style="margin:0; font-size:14px; color:#4b5563;">
                  Access code
                </p>

                <!-- Access code value -->
                <p style="margin:4px 0 24px 0; font-size:26px; font-weight:600; letter-spacing:2px; color:#111827;">
                  {Access Code}
                </p>

                <!-- Primary button -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 16px auto;">
                  <tr>
                    <td align="center" bgcolor="#2563eb" style="border-radius:6px;">
                      <a href="{Connect URL}"
                         style="display:inline-block; padding:12px 28px; font-family:system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:15px; font-weight:600; text-decoration:none; color:#ffffff; border-radius:6px;">
                        CONNECT BANK ACCOUNT
                      </a>
                    </td>
                  </tr>
                </table>

                <!-- Helper text -->
                <p style="margin:0 0 8px 0; font-size:13px; color:#6b7280;">
                  Enter the access code above after clicking the button to securely link your bank account.
                </p>

                <!-- Fallback link -->
                <p style="margin:8px 0 0 0; font-size:11px; color:#9ca3af;">
                  If the button doesn't work, copy and paste this link into your browser:<br />
                  <span style="word-break:break-all;">{Connect URL}</span>
                </p>
              </td>
            </tr>

            <!-- Optional footer -->
            <tr>
              <td style="padding:16px 24px 24px 24px; text-align:center; font-family:system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:11px; color:#9ca3af;">
                You received this email because {Company Name} uses our service for secure bank account connections.
              </td>
            </tr>
          </table>
          <!-- /Main card -->
        </td>
      </tr>
    </table>
  </body>
</html>
HTML;
        $safe_replacements = [
            '{User Name}'    => htmlspecialchars($user_full_name, ENT_QUOTES, 'UTF-8'),
            '{Company Name}' => htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'),
            '{Access Code}'  => htmlspecialchars($code, ENT_QUOTES, 'UTF-8'),
            '{Connect URL}'  => htmlspecialchars($link, ENT_QUOTES, 'UTF-8'),
        ];
        $mail->Body    = strtr($body, $safe_replacements);
        $mail->AltBody = strtr('Use the access code {Access Code} to connect your bank account at {Connect URL}.', $safe_replacements);
        $mail->send();
        return true;
    }
    public function send_user_invite($email, $invite_code, $link, $email_password) {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'corsaire.tech@gmail.com';
        $mail->Password   = $email_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->SMTPDebug  = 0;
        $mail->setFrom('corsaire.tech@gmail.com', 'Corsaire CRM');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'You have been invited to Corsaire CRM';

        $safe_replacements = [
            '{Invite Code}' => htmlspecialchars($invite_code, ENT_QUOTES, 'UTF-8'),
            '{Invite URL}'  => htmlspecialchars($link, ENT_QUOTES, 'UTF-8'),
        ];

        $body = <<<HTML
<!doctype html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Corsaire CRM Invite</title>
  </head>
  <body style="margin:0; padding:0; background-color:#f5f5f5;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f5f5f5; padding:24px 0;">
      <tr>
        <td align="center">
          <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px; background-color:#ffffff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06);">
            <tr>
              <td style="padding:32px 24px 16px 24px; text-align:center; font-family:system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color:#111827;">
                <h1 style="margin:0 0 16px 0; font-size:22px; font-weight:600; line-height:1.3;">
                  You're invited to CorsaireCRM
                </h1>
                <p style="margin:0 0 24px 0; font-size:15px; line-height:1.6; color:#4b5563;">
                  You've been invited to create a CorsaireCRM account. Please copy the code and enter it at the link below:
                </p>
                <p style="margin:0; font-size:14px; color:#4b5563;">
                  Invite code
                </p>
                <p style="margin:4px 0 24px 0; font-size:26px; font-weight:600; letter-spacing:2px; color:#111827;">
                  {Invite Code}
                </p>
                <div style="margin:16px 0 12px 0; font-size:14px; color:#4b5563; text-align:center;">
                  <div style="font-weight:600; margin-bottom:8px;">Steps to get started:</div>
                  <ol style="display:inline-block; text-align:left; margin:0 auto 16px auto; padding-left:18px; color:#4b5563; font-size:14px; line-height:1.5;">
                    <li>Click "Sign Up" below.</li>
                    <li>Click Login, then Sign Up with Code.</li>
                    <li>Enter the code above.</li>
                  </ol>
                </div>
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 16px auto;">
                  <tr>
                    <td align="center" bgcolor="#2563eb" style="border-radius:6px;">
                      <a href="{Invite URL}"
                         style="display:inline-block; padding:12px 28px; font-family:system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:15px; font-weight:600; text-decoration:none; color:#ffffff; border-radius:6px;">
                        Sign Up
                      </a>
                    </td>
                  </tr>
                </table>
                <p style="margin:8px 0 0 0; font-size:12px; color:#9ca3af;">
                  If the button doesn't work, copy and paste this link into your browser:<br />
                  <span style="word-break:break-all;">{Invite URL}</span>
                </p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
HTML;

        $mail->Body    = strtr($body, $safe_replacements);
        $mail->AltBody = strtr("You've been invited to create a CorsaireCRM account.\nInvite code: {Invite Code}\nVisit: {Invite URL}", $safe_replacements);
        $mail->send();
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
