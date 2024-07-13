<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/../includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../includes/error-reporting.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../includes/default-time-zone.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../includes/mailer-test.php');
// require_once FileUtils::normalizeFilePath(__DIR__ . '/../includes/mailer.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../includes/classes/email-sender.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../includes/classes/email-queue.php');

$emails = EmailQueue::getCurrQueue();

// print_r($emails);

$send_email = new EmailSender($mail);

foreach ($emails as $email) {
    $is_send_success = $send_email->sendQueuedMail($email['content']);

    if ($is_send_success) {
        EmailQueue::updateEmailStatus($email['email_id'], 'sent');
    } else {
        EmailQueue::updateEmailStatus($email['email_id'], 'failed');
    }
}

?>

a