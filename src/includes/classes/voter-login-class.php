<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/file-utils.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/db-connector.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/manage-ip-address.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/logger.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../session-handler.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../error-reporting.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../default-time-zone.php');

class Login extends IpAddress {

    private const LOGIN_BLOCK_TIME = 1800; // 30 minutes
    private const LOGIN_ATTEMPT_COUNT = 5;
    private $connection;
    private $ip_address;
    private $ip_manager;
    private $error_message;
    private $info_message;
    private $logger;

    // Creates connection to the database and gets user ip address
    public function __construct() {
        $this->connection = DatabaseConnection::connect();
        $this->ip_address = IpAddress::getIpAddress();
        $this->ip_manager = new IpAddress();
        $this->error_message = 'error_message';
        $this->info_message = 'info_message';
    }

    // Authenticates the user submitted email
    protected function getUser($email, $password) {
        if($this->isBlocked()) {
            $this->isLoginAttemptMax();
        }

        // Verifies user in the voter table
        $sql = "SELECT voter_id, email, password, role, account_status, voter_status, vote_status FROM voter WHERE BINARY email = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if email exists
        if ($result->num_rows === 0) {
            $this->handleUserNotFound();
        } else {
            $row = $result->fetch_assoc();
            $this->handlePasswordVerification($row, $password);
        }

        $stmt->close();
    }

    // Check login attempts and if is blocked
    private function isBlocked() {
        $time = time() - self::LOGIN_BLOCK_TIME;
        $check_attempts = $this->ip_manager->countIpAddressAttempt($this->ip_address, $time);
        return $check_attempts >= self::LOGIN_ATTEMPT_COUNT;
    }

    // Check if password matches the hashed password
    private function handlePasswordVerification($row, $password) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['role'] = $row['role'];
            $_SESSION['account_status'] = $row['account_status'];
            $this->handleUserRole($row);
        } else {
            $this->handleMismatchedCredentials($row);
        }       
    }

    // Check user role
    private function handleUserRole($row) {
        switch ($row['role']) {
            case 'student_voter':
                $this->handleStudentVoter($row);
                break;
            case 'admin':
            case 'head_admin':
                $this->handleAdminOrHead($row);
                break;
            default:
                $this->redirectWithMessage($this->error_message, 'Role not found in session.');
                break;
        }
    }

    // Check student-voter account and vote status
    private function handleStudentVoter($row) {
        $this->ip_manager->deleteIpAddress($this->ip_address);

        switch ($row['account_status']) {
            case 'for_verification':
                $this->redirectWithMessage($this->info_message, 'This account is under verification.');
                break;
            case 'invalid':
                $this->redirectWithMessage($this->info_message, 'This account was rejected.');
                break;
            case 'verified':
                $this->handleVerifiedStudentVoter($row);
                break;
            default:
                $this->redirectWithMessage($this->error_message, 'Something went wrong.');
                break;
        }
    }

    // Check if election year is open	
    private function isElectionYearOpen() {	
        $sql = "SELECT start, close FROM election_schedule WHERE schedule_id = 0";	
        $stmt = $this->connection->prepare($sql);	
        $stmt->execute();	
        $result = $stmt->get_result();	

        if($result) {	
            $row = $result->fetch_assoc();	
            $today = new DateTime();	
            $start = new Datetime($row['start']);	
            $close = new DateTime($row['close']);	
            if($today >= $start && $today <= $close) {	
                $_SESSION['electionOpen'] = true;
                $this->storeLoginActivity();                
                $this->redirectTo('../ballot-forms.php');
            }	
            else {	
                $_SESSION['electionOpen'] = false;
                $this->storeLoginActivity();	
                $this->redirectTo('../voting-closed.php');
            }	
        }	
        else {	
            $this->redirectWithMessage('Something went wrong.');	
        }	
        $stmt->close();	
    }

    // Check voter status of a verified account
    private function handleVerifiedStudentVoter($row) {    
        $_SESSION['voter_status'] = $row['voter_status'];
        $_SESSION['vote_status'] = $row['vote_status'];

        if ($row['voter_status'] === 'inactive') {
            $this->redirectWithMessage($this->info_message, 'This account is inactive.');
        } else {
            $_SESSION['voter_id'] = $row['voter_id'];
            $this->redirectBasedOnVoteStatus($row['vote_status']);
        }
    }

    // Check voter's vote status (e.g., if the user has voted already or no)
    private function redirectBasedOnVoteStatus($vote_status) {
        $this->regenerateSessionId();

        switch ($vote_status) {
            case NULL:
                $this->isElectionYearOpen();
                break;
            case 'voted':
            case 'abstained':
                $this->storeLoginActivity();
                $this->redirectTo('../end-point.php');
                break;
            default:
                $this->redirectTo('../landing-page.php');
                break;
        }
        exit();
    }

    // Redirects a committee member to the admin dashboard
    private function handleAdminOrHead($row) {
        $this->ip_manager->deleteIpAddress($this->ip_address);

        if ($row['account_status'] === 'verified') {
            $this->regenerateSessionId();
            $_SESSION['voter_id'] = $row['voter_id'];
            $this->storeLoginActivity();
            $this->redirectTo('../admindashboard.php');
        } else {
            $this->redirectWithMessage($this->info_message, 'This account has been disabled.');
        }
        exit();
    }

    // Check mismatched email and password
    private function handleMismatchedCredentials($row) {
        $this->ip_manager->storeIpAddress($this->ip_address, time());
        $remaining_attempt = self::LOGIN_ATTEMPT_COUNT - $this->getFailedAttemptsCount();
        
        if ($remaining_attempt <= 0) {
            $this->isLoginAttemptMax();        
        } 
        else {
            $this->redirectWithMessage($this->error_message, 'Email and password do not match.<br/><strong>' . $remaining_attempt . ' remaining attempts.</strong>');
        }
    }

    // If email does not exist, redirects/remains on the login page
    private function handleUserNotFound() {
        $this->ip_manager->storeIpAddress($this->ip_address, time());
        $remaining_attempt = self::LOGIN_ATTEMPT_COUNT - $this->getFailedAttemptsCount();
        
        if ($remaining_attempt <= 0) {
            $this->isLoginAttemptMax();        
        } 
        $this->redirectWithMessage($this->error_message, 'Email and password do not match.<br/><strong>' . $remaining_attempt . ' remaining attempts.</strong>');
    }

    // Counts user failed login attempts
    private function getFailedAttemptsCount() {
        $time = time() - self::LOGIN_BLOCK_TIME;
        return $this->ip_manager->countIpAddressAttempt($this->ip_address, $time);
    }

    // Regenerate a stronger session
    private function regenerateSessionId() {   
        session_regenerate_id(true);
    }

    // Handles different types of messages
    private function redirectWithMessage($type, $message) {
        $_SESSION[$type] = $message;
        $this->redirectTo('../voter-login.php');
    }
    
    private function isLoginAttemptMax() {
        $_SESSION['maxLimit'] = true;
        $this->redirectTo('../voter-login.php');
    }


    private function storeLoginActivity() {
        $this->logger = new Logger($_SESSION['role'], LOGIN);
        $this->logger->logActivity();
    }


    private function redirectTo($location) {
        header("Location: " . $location);
        exit();
    }
}