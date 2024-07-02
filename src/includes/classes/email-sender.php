<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/file-utils.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../error-reporting.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../default-time-zone.php');

class EmailSender
{
    private $mail;
    private $app_url = 'https://ivote-pupsrc.com/';

    public function __construct($mail)
    {
        $this->mail = $mail;
    }

    public function sendApprovalEmail($recipientEmail)
    {
        $mailBody = 'Good day, Iskolar!<br><br>
            
        We’re pleased to inform you that your account has been <b>approved</b>! You can now access and log in to <a href="' . $this->app_url . '">iVOTE</a>. <br><br>
            
        If you have any questions or need assistance, please contact the support team at ivotepupsrc@gmail.com. ';

        error_log($recipientEmail);
        return $this->sendEmail($recipientEmail, 'iVOTE Account Approval', $mailBody);
    }


    public function sendForVerificationStatus($recipientEmail) {
        $mailBody = 'Good day, Iskolar!<br><br>
            
        We wanted to inform you that your account registration has been successfully submitted and is currently awaiting approval. We will review your information shortly and notify you via email once your account has been verified.<br><br>

        If you have any questions or need assistance, please contact the support team at ivotepupsrc@gmail.com. ';

        return $this->sendEmail($recipientEmail, 'iVOTE Account Registration', $mailBody);
    }


    public function sendRejectionEmail($recipientEmail, $reason, $otherReason = '')
    {
        $mailBody = 'Good day, Iskolar!<br><br>
            
        We regret to inform you that your recent account registration has been rejected. <br><br>
            
        <b>Reason for rejection:</b> ';

        if ($reason == 'reason1') {
            $mailBody .= 'Student is not part of the organization';
        } elseif ($reason == 'reason2') {
            $mailBody .= 'The PDF is low quality and illegible';
        } elseif ($reason == 'others') {
            $mailBody .= htmlspecialchars($otherReason);
        }

        $mailBody .= '<br><br>If you have any questions or need assistance, please contact the support team at ivotepupsrc@gmail.com. </h5>';

        return $this->sendEmail($recipientEmail, 'iVOTE Registration Rejected', $mailBody);
    }


    public function sendPasswordEmail($recipientEmail, $password) {
        $subject = 'iVOTE Admin Account Created and Password';
        $mailBody = "Hello, Committee Member.<br><br>";
        $mailBody .= "We're pleased to inform you that your account has been successfully created. 
        Below, you'll find your generated password. <br><br>";

        $mailBody .= "Password: $password <br><br>";
        $mailBody .= "For security purposes, we recommend that you log in to your account and change your passsword
        after your first login. If you have any questions or need assistance, contact the support team at ivotepupsrc@gmail.com.";
    
        return $this->sendEmail($recipientEmail, $subject, $mailBody);
    }

    public function sendPasswordResetEmail($recipientEmail, $token, $orgName) {
        $subject = 'iVOTE Password Reset Request';
        $resetPasswordLink = $this->app_url . "reset-password?token=" . urlencode($token) . "&orgName=" . urlencode($orgName);
        
        $mailBody = <<<EOT
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>iVOTE Password Reset Request</title>
        </head>
        <body>
            <p>Dear Iskolar,</p>
            <p>We have received a request to reset the password associated with your account. To complete the process, 
            please follow the instructions below:</p>
            <ul style="padding-left: 20px;">
                <li>Click on the following link to reset your password: <a href="$resetPasswordLink">Reset Password Link</a></li>
                <li>The password reset link is only available for 30 minutes.</li>
                <li>If you are unable to click the link above, please copy and paste it into your browser's address bar.</li>
                <li>Once the link opens, you will be prompted to enter a new password for your account. Please choose a strong and secure password to ensure the safety of your account.</li>
            </ul>
            <p>If you did not initiate this password reset request, please disregard this email.</p>
            <p>Thank you for your attention to this matter.</p>
            <p>Best regards,<br>iVOTE Team</p>
        </body>
        </html>
        EOT;
    
        return $this->sendEmail($recipientEmail, $subject, $mailBody);
    }
    
    public function sendResetEmail($recipientEmail, $token, $orgName) {
        $subject = 'iVOTE Change Email Request';
        $updateEmailLink = "http://localhost/PUPSRC-AutomatedElectionSystem/src/setting-email-update.php?token=$token&orgName=$orgName";
        $mailBody = <<<EOT
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>iVOTE Password Change Email Request</title>
        </head>
        <body">
            <p>Dear Iskolar,</p>
            <p>We have received a request to change your email associated with your account. To complete the process, 
            please follow the instructions below:</p>
            <ul style="padding-left: 20px;">
                <li>Click on the following link to change your email: <a href="$updateEmailLink">Change Email Link</a></li>
                <li>The change email link is only available for 30 minutes.</li>
                <li>If you are unable to click the link above, please copy and paste it into your browser's address bar.</li>
                <li>Once the link opens, you will be prompted to enter a new email for your account.</li>
            </ul>
            <p>If you did not initiate this change email request, please disregard this email.</p>
            <p>Thank you for your attention to this matter.</p>
            <p>Best regards,<br>iVOTE Team</p>
        </body>
        </html>
        EOT;
    
        return $this->sendEmail($recipientEmail, $subject, $mailBody);
    }
    

    private function sendEmail($recipientEmail, $subject, $body)
    {
        try {
            $this->mail->setFrom('ivotepupsrc@gmail.com', 'iVOTE');
            $this->mail->addAddress($recipientEmail);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}