<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, '../includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath('../includes/classes/db-connector.php');
require_once FileUtils::normalizeFilePath('../includes/session-handler.php');
require_once FileUtils::normalizeFilePath('../includes/classes/query-handler.php');

include FileUtils::normalizeFilePath('../includes/session-exchange.php');

if (isset($_POST['voter_id'])) {
    $voter_id = $_POST['voter_id'];
    
    // Establish database connection
    $conn = DatabaseConnection::connect();
    
    // Initialize the query executor
    $queryExecutor = new QueryExecutor($conn);
    
    // Query to fetch the details based on voter_id
    $query = "SELECT  acc_created, email, status_updated, role, first_name, middle_name, last_name, suffix FROM voter WHERE voter_id = ?";
    
    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $voter_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Output JSON response with voter details, replacing NULL values with empty strings
        echo json_encode([
            'status_updated' => $row['status_updated'] ?? '',
            'acc_created' => $row['acc_created'] ?? '',
            'email' => $row['email'] ?? '',
            'role' => $row['role'] ?? '',
            'first_name' => $row['first_name'] ?? '',
            'middle_name' => $row['middle_name'] ?? '',
            'last_name' => $row['last_name'] ?? '',
            'suffix' => $row['suffix'] ?? ''
        ]);
    } else {
        echo json_encode(['error' => 'No record found']);
    }
    
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
