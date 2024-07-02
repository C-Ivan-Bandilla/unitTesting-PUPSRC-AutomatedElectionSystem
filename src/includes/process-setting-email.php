<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/classes/file-utils.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/session-handler.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/classes/session-manager.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/session-exchange.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/classes/db-connector.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/error-reporting.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/default-time-zone.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST['token'];
   $email = trim($_POST['email']);
    $password_confirmation = trim($_POST['password_confirmation']);
    $token_hash = hash("sha256", $token);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Invalid email format.';
        redirectToUpdatePageWithError($token);
    }

    $connection = DatabaseConnection::connect();

    // Check if the reset token exists and is valid
    $sql = "SELECT * FROM voter WHERE reset_token_hash = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        $_SESSION['error_message'] = 'Your email change link was not found.';
        redirectToLoginPage();
    }

    // Check if the reset token has expired
    $expiry_time = strtotime($row["reset_token_expires_at"]);
    $current_time = time();

    if ($expiry_time <= $current_time) {
        $_SESSION['error_message'] = 'Your email change link has expired.';
        redirectToLoginPage();
    }

    // Retrieve the current password hash from the database
    $current_password_hash = $row['password'];

    // Verify if the password_confirmation matches the current password
    if (!password_verify($password_confirmation, $current_password_hash)) {
        $_SESSION['error_message'] = 'Incorrect password. Please try again.';
        redirectToUpdatePageWithError($token);
    }

    // Check if the new email is already used by another user
    $sql_check_email = "SELECT * FROM voter WHERE email = ?";
    $stmt_check_email = $connection->prepare($sql_check_email);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $result_check_email = $stmt_check_email->get_result();
    $existing_user = $result_check_email->fetch_assoc();

    if ($existing_user) {
        $_SESSION['error_message'] = 'Email address is already in use. Please choose another one.';
        redirectToUpdatePageWithError($token);
    }

    // Update the email address in the database
    $sql_update = "UPDATE voter SET email = ? WHERE reset_token_hash = ?";
    $stmt_update = $connection->prepare($sql_update);
    $stmt_update->bind_param('ss', $email, $token_hash);
    $success = $stmt_update->execute();

    if ($success) {
        $_SESSION['success_message'] = 'Your email has been updated successfully.';
        header("Location: ../user-setting-information.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to update your email. Please try again.";
        redirectToUpdatePageWithError($token);
    }
} else {
    $_SESSION['error_message'] = "Invalid request method.";
   // redirectToLoginPage();
}

function redirectToUpdatePageWithError($token) {
    global $org_name;
    header("Location: ../setting-email-update.php?token=" . urlencode($token) . "&orgName=" . urlencode($org_name));
    exit();
}

function redirectToLoginPage() {
    header("Location: ../voter-login.php");
    exit();
}
?>