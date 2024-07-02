<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once str_replace('/', DIRECTORY_SEPARATOR, '../includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath('../includes/classes/db-connector.php');
require_once FileUtils::normalizeFilePath('../includes/session-handler.php');
require_once FileUtils::normalizeFilePath('../includes/classes/session-manager.php');
require_once FileUtils::normalizeFilePath('../includes/classes/query-handler.php');

$conn = DatabaseConnection::connect();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$queryExecutor = new QueryExecutor($conn);

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'candidate_creation';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';
$filter = isset($_GET['filter']) ? $_GET['filter'] : [];

// Validate sort_by parameter to prevent SQL injection
$valid_sort_columns = ['candidate_creation', 'first_name'];
if (!in_array($sort_by, $valid_sort_columns)) {
    $sort_by = 'candidate_creation';
}

// Validate sort_order parameter to prevent SQL injection
$sort_order = strtoupper($sort_order);
if ($sort_order !== 'ASC' && $sort_order !== 'DESC') {
    $sort_order = 'DESC';
}

// Prepare the filter condition
$filterCondition = "";
if (!empty($filter)) {
    $filterCondition = "AND p.title IN (" . implode(',', array_map(function ($position) use ($conn) {
        return "'" . $conn->real_escape_string($position) . "'";
    }, $filter)) . ")";
} 

$query = "SELECT c.candidate_id, c.first_name, c.middle_name, c.last_name, c.suffix, c.position_id, p.title as position, c.section, c.candidate_creation
          FROM candidate c
          JOIN position p ON c.position_id = p.position_id
          WHERE c.candidacy_status = 'Verified'
          $filterCondition
          ORDER BY $sort_by $sort_order
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
if ($result === false) {
    die("Execute failed: " . $stmt->error);
}

$candidates = [];
while ($row = $result->fetch_assoc()) {
    $candidates[] = $row;
}

$stmt->close();

$countQuery = "SELECT COUNT(*) as total
               FROM candidate c
               JOIN position p ON c.position_id = p.position_id
               WHERE c.candidacy_status = 'Verified'
               $filterCondition";
$countStmt = $conn->prepare($countQuery);
if ($countStmt === false) {
    die("Prepare failed: " . $conn->error);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
if ($countResult === false) {
    die("Execute failed: " . $countStmt->error);
}
$totalRows = $countResult->fetch_assoc()['total'];

$countStmt->close();

echo json_encode([
    'candidates' => $candidates,
    'totalRows' => $totalRows
]);
?>
