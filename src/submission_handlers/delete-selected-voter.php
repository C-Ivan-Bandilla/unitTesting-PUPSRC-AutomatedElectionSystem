<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, '../includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath('../includes/classes/db-connector.php');
require_once FileUtils::normalizeFilePath('../includes/classes/logger.php');
require_once FileUtils::normalizeFilePath('../includes/session-handler.php');
require_once FileUtils::normalizeFilePath('../includes/classes/query-handler.php');

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the 'ids' parameter is set in the POST request
    if (isset($_POST['ids'])) {
        $ids = $_POST['ids'];
        
        // Initialize logger based on the number of IDs
        if (count($ids) == 1) {
            $logger = new Logger($_SESSION['role'], PERMANENT_DELETE_VOTER);
        } else {
            $logger = new Logger($_SESSION['role'], PERMANENT_DELETE_MULTIPLE_VOTERS);
        }
        $logger->logActivity();
        
        // Establish database connection
        $conn = DatabaseConnection::connect();
        
        // Initialize the query executor
        $queryExecutor = new QueryExecutor($conn);
        
        // Prepare the SQL statement to delete selected items
        $query = "DELETE FROM voter WHERE voter_id IN (";
        $params = array();
        foreach ($ids as $id) {
            $query .= "?, ";
            $params[] = $id;
        }
        // Replace the last comma and space with a closing parenthesis
        $query = rtrim($query, ", ") . ")";
        
        // Execute the delete query
        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat("i", count($ids)), ...$params); // Bind parameters dynamically
        $stmt->execute();
        
        // Check if any rows were affected
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Selected items deleted successfully']);
        } else {
            echo json_encode(['error' => 'No items deleted']);
        }
        
        // Close statement and connection
        $stmt->close();
        $conn->close();
    } else {
        // Return an error response if 'ids' parameter is not set
        echo json_encode(['error' => 'No IDs provided for deletion']);
    }
} else {
    // Return an error response if the request method is not POST
    echo json_encode(['error' => 'Invalid request method']);
}
?>
