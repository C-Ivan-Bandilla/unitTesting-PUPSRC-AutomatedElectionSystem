<?php
// Include Composer's autoloader
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
} else {
    die("Composer autoloader not found. Please check your installation.");
}

require_once '../includes/classes/db-connector.php';
require_once '../includes/session-handler.php';
require_once '../includes/classes/session-manager.php';
require_once '../includes/classes/query-handler.php';

// Check if PhpSpreadsheet is installed
if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
    die("PhpSpreadsheet library not found. Please check your Composer installation and autoloader.");
}

use PhpOffice\PhpSpreadsheet\IOFactory;

function validateHeaders($headers) {
    $expected_headers = ['Last Name', 'First Name', 'Middle Name', 'Suffix', 'Year Level', 'Section', 'Email'];
    return $headers === $expected_headers;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['voter_id']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'head_admin')) {
    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    try {
        $conn = DatabaseConnection::connect();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => "Database connection failed: " . $e->getMessage()]);
        exit;
    }

    if ($fileExtension == 'csv') {
        $result = importCSV($fileTmpName, $conn);
    } elseif ($fileExtension == 'xls' || $fileExtension == 'xlsx') {
        $result = importExcel($fileTmpName, $conn);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Invalid file format. Please upload a CSV or Excel file."]);
        exit;
    }

    $conn->close();
    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => "Invalid request or insufficient permissions"]);
}

function generatePassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

function importCSV($filePath, $conn) {
    $file = fopen($filePath, 'r');
    $headers = fgetcsv($file);
    
    if (!validateHeaders($headers)) {
        fclose($file);
        return ['status' => 'error', 'message' => "Invalid headers. Please ensure the headers are in the correct order."];
    }
    
    $count = 0;
    $duplicates = [];

    while (($data = fgetcsv($file)) !== FALSE) {
        $result = insertData($data, $conn);
        if ($result === true) {
            $count++;
        } elseif ($result === 'duplicate') {
            $duplicates[] = $data[6];
        }
    }

    fclose($file);
    
    if (!empty($duplicates)) {
        return ['status' => 'warning', 'message' => "Import completed with duplicates. Rows imported: $count", 'duplicates' => $duplicates];
    }
    
    return ['status' => 'success', 'message' => "CSV import completed. Rows imported: $count"];
}

function importExcel($filePath, $conn) {
    $spreadsheet = IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    
    $headers = $rows[0];
    if (!validateHeaders($headers)) {
        return ['status' => 'error', 'message' => "Invalid headers. Please ensure the headers are in the correct order."];
    }
    
    array_shift($rows); // Remove header row
    $count = 0;
    $duplicates = [];

    foreach ($rows as $row) {
        $result = insertData($row, $conn);
        if ($result === true) {
            $count++;
        } elseif ($result === 'duplicate') {
            $duplicates[] = $row[6];
        }
    }

    if (!empty($duplicates)) {
        return ['status' => 'warning', 'message' => "Import completed with duplicates. Rows imported: $count", 'duplicates' => $duplicates];
    }
    
    return ['status' => 'success', 'message' => "Excel import completed. Rows imported: $count"];
}

function insertData($data, $conn) {
    // Check if student already exists
    $checkSql = "SELECT * FROM voter WHERE email = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $data[6]);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        $checkStmt->close();
        return 'duplicate';
    }
    $checkStmt->close();

    $role = 'student_voter';
    $accountStatus = 'for_verification';
    $voterStatus = 'active';
    $voteStatus = NULL;
    
    $password = generatePassword();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $suffix = empty($data[3]) ? NULL : $data[3];

    $sql = "INSERT INTO voter (last_name, first_name, middle_name, suffix, year_level, section, email, password, role, account_status, voter_status, vote_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssss", $data[0], $data[1], $data[2], $suffix, $data[4], $data[5], $data[6], $hashedPassword, $role, $accountStatus, $voterStatus, $voteStatus);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    } catch (Exception $e) {
        return false;
    }
}