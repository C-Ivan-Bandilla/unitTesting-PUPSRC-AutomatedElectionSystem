<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/file-utils.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/db-config.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../session-handler.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/../error-reporting.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/../default-time-zone.php');

class Registration {
    private $email;
    private $password;
    private $confirm_password;
    private $organization;
    private $file;
    private $file_hash;
    private $file_name;
    private $connection;
    private const ACCOUNT_STATUS = 'for_verification';
    private const ROLE = 'student_voter';

    public function __construct($email, $password, $confirm_password, $organization, $file) {
        $this->email = $email;
        $this->password = $password;
        $this->confirm_password = $confirm_password;
        $this->organization = $organization;
        $this->file = $file;

        $this->initializeDatabaseConnection();
    }

    // Initialize database connection to organization db
    private function initializeDatabaseConnection() {
        $config = DatabaseConfig::getOrganizationDBConfig($this->organization);
        $this->connection = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
    }

    // Call every validation and insertion methods
    public function processRegistrationCredentials() {
        try {        
            unset($_SESSION['registration_success']);
            unset($_SESSION['error_message']);
            $this->validateEmailAndPasswordLength();
            $this->validateEmailFormat();
            $this->validateEmailNotExist();
            $this->validatePasswords();
            $this->validateFile();
            $this->saveFile();
            $this->saveFileToSco();
            $this->beginTransaction();
            $this->insertIntoOrganizationDB();
            $this->insertIntoScoDB();
            $this->commitTransaction();
            $this->sendEmailNotice();
            $_SESSION['registration_success'] = true;
            header("Location: ../register.php");
            exit();
        }
        catch (Exception $e) {
            $this->rollbackTransaction();
            $error_message = $e->getMessage();
            $_SESSION['error_message'] = $error_message;
            header("Location: ../register.php");
            exit();
        }
    }

    // Validate email and password string length to avoid buffer overflow
    private function validateEmailAndPasswordLength() {
        if (strlen($this->email) > 255) {
            throw new Exception("Email address must not exceed 255 characters");
        }

        if (strlen($this->password) > 20) {
            throw new Exception("Password must not exceed 20 characters");
        }
    }

    // Check for invalid email format
    private function validateEmailFormat() {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please provide a valid email address, such as johndoe@gmail.com.");
        }
    }

    // Additional to check if email address already exists
    private function validateEmailNotExist() {
        $config = DatabaseConfig::getOrganizationDBConfig($this->organization);
        $connection = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
        
        $sql = "SELECT email FROM voter WHERE BINARY email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            throw new Exception("This email is already taken.");
        }

        $stmt->close();
        $connection->close();  
    }

    // Check if password and retype password matches
    private function validatePasswords() {
        if($this->password !== $this->confirm_password) {
            throw new Exception("Your passwords do not match.");
        }
    }

    // Validate the attached file if it's PDF and doesn't exceed 25mb
    private function validateFile() {
        $target_directory = "../user_data/{$this->organization}/cor/";
        if(!file_exists($target_directory)) {
            mkdir($target_directory, 0777, true);
        }

        // Check if file is PDF and within size limit
        $file_type = strtolower(pathinfo($this->file['name'], PATHINFO_EXTENSION));
        if($file_type != 'pdf' || $this->file['size'] > 25000000) {
            throw new Exception("Invalid file. Only PDF files up to 25MB are allowed.");
        }

        $this->file_name = basename($this->file['name']);
    }

    // Save the pdf file in user_data/org_name/cor/
    private function saveFile() {
        $target_directory = "../user_data/{$this->organization}/cor/";
        $target_file = $target_directory . $this->file_name;

        if(!move_uploaded_file($this->file["tmp_name"], $target_file)) {
            throw new Exception("There was an error uploading your file. Try again");
        }

        $this->file_hash = hash_file('sha256', $target_file);
    }

    // Save the pdf file in user_data/sco/cor/
    private function saveFileToSco() {
        $sco_directory = "../user_data/sco/cor/";
        if (!file_exists($sco_directory)) {
            mkdir($sco_directory, 0777, true);
        }
        
        $sco_file = $sco_directory . $this->file_name;

        if (!copy("../user_data/{$this->organization}/cor/" . $this->file_name, $sco_file)) {
            throw new Exception("There was an error copying your file to the SCO directory. Try again");
        }
    }

    // Insert from picked org from dropdown into its database
    private function insertIntoOrganizationDB() {
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO voter (email, password, cor, cor_hash, account_status, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);

        $account_status = self::ACCOUNT_STATUS;
        $role = self::ROLE;

        $stmt->bind_param("ssssss", $this->email, $hashed_password, $this->file_name, $this->file_hash, $account_status, $role);
        if(!$stmt->execute()) {
            throw new Exception("Error occurred during registration for " . strtoupper($this->organization) . ".");
        }

        $stmt->close();
    }

    // Insert another set of data into the db_sco
    private function insertIntoScoDB() {
        $sco = 'sco';
        $config = DatabaseConfig::getOrganizationDBConfig($sco);
        $sco_connection = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);

        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO voter (email, password, cor, cor_hash, account_status, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $sco_connection->prepare($sql);

        $account_status = self::ACCOUNT_STATUS;
        $role = self::ROLE;

        $stmt->bind_param("ssssss", $this->email, $hashed_password, $this->file_name, $this->file_hash, $account_status, $role);

        if(!$stmt->execute()) {
            throw new Exception("Error occurred during registration.");
        }

        $stmt->close();
        $sco_connection->close();
    }

    // Send an email notice to user
    private function sendEmailNotice() {
        include_once FileUtils::normalizeFilePath(__DIR__ . '/../mailer.php');
        include_once FileUtils::normalizeFilePath(__DIR__ . '/email-sender.php');
        $mailer = new EmailSender($mail);
        $mailer->sendForVerificationStatus($this->email);
    }

    // Start transaction into db but no insertion until commit is made or call
    private function beginTransaction() {
        $this->connection->begin_transaction();
    }

    // Make insertion permanent if there are no exceptions
    private function commitTransaction() {
        $this->connection->commit();
    }

    // Undo transaction if there are catched exceptions
    private function rollbackTransaction() {
        $this->connection->rollback();
    }

    // Closed database connection when no longer referenced
    public function __destruct() {
        if($this->connection) {
            $this->connection->close();
        }
    }

}