<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/file-utils.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/db-connector.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/manage-ip-address.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../session-handler.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../constants.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../default-time-zone.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../session-exchange.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../error-reporting.php');

/*
 * This is a utility class for logging the activity of your page user.
 * You can configure the constants.php file located in includes directory
 * for additional actions and such.
 * To log the activity of your user, you can instantiate a new instance of
 * this class and pass the $role and $action arguments and then call the 
 * logActivity() method on your endpoint.
 * 
 * Example Usage using a superglobal session variable:
 * 
 * $logger = new Logger($_SESSION['role'], LOGIN);
 * $logger->logActivity();
 * 
 * 
 * You can also explicitly pass the role of your user as the argument.
 * Example: 
 * 
 * $logger = new Logger(ROLE_ADMIN, LOGIN);
 * $logger->logActivity();
*/

class Logger {
    private $connection;
    private $timestamp;
    private $ip_address;
    private $browser;
    private $voter_id;
    private $role;
    private $action;


    public function __construct($role, $action) {
        $this->connection = DatabaseConnection::connect();
        $this->ip_address = IpAddress::getIpAddress();
        $browserInfo = $this->getBrowserAndVersion();
        $this->browser = $browserInfo['browser_name'] . ' ' . $browserInfo['browser_version'];
        $this->voter_id = $_SESSION['voter_id'];
        $this->timestamp = date("Y-m-d H:i:s");

        $this->role = $role;
        $this->action = $action;

        // Maps action based on role
        $this->role = $role;
        switch($this->role) {
            case ROLE_HEAD_ADMIN:
                $this->action = HEAD_ADMIN_ACTIONS[$action];
                break;
            case ROLE_ADMIN:
                $this->action = ADMIN_ACTIONS[$action];
                break;
            case ROLE_STUDENT_VOTER:
                $this->action = STUDENT_VOTER_ACTIONS[$action];
                break;
        }
    }


    private function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? NULL;
    }


    private function getBrowserName($user_agent) {
        $browser = 'Unknown';
    
        if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
            $browser = 'Internet Explorer';
        } elseif (preg_match('/Edg/i', $user_agent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Firefox/i', $user_agent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/OPR/i', $user_agent)) {
            $browser = 'Opera';
        } elseif (preg_match('/Chrome/i', $user_agent) && !preg_match('/Edg/i', $user_agent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $user_agent) && !preg_match('/Edge/i', $user_agent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Netscape/i', $user_agent)) {
            $browser = 'Netscape';
        } elseif (preg_match('/Trident/i', $user_agent)) {
            $browser = 'Internet Explorer';
        }
    
        return $browser;
    }
    
    private function getBrowserVersion($user_agent, $browser_name) {
        $version = '';
    
        if ($browser_name === 'Edge' && preg_match('/Edg\/([\d\.]+)/i', $user_agent, $matches)) {
            $version = $matches[1];
        } elseif (preg_match('/' . preg_quote($browser_name) . '\/([\d\.]+)/i', $user_agent, $matches)) {
            $version = $matches[1];
        }
    
        return $version ?: '';
    }

    public function getBrowserAndVersion() {
        $user_agent = $this->getUserAgent();
        $browser_name = $this->getBrowserName($user_agent);
        $browser_version = $this->getBrowserVersion($user_agent, $browser_name);

        return array(
            'user_agent' => $user_agent,
            'browser_name' => $browser_name,
            'browser_version' => $browser_version
        );
    }


    public function logActivity() {
        $sql = "INSERT INTO activity_log (timestamp, ip_address, browser, voter_id, action) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("sssis", $this->timestamp, $this->ip_address, $this->browser, $this->voter_id, $this->action);
        $stmt->execute();
        $stmt->close();
    }
}
