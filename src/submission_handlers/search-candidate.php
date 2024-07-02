<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, '../includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath('../includes/classes/db-connector.php');
require_once FileUtils::normalizeFilePath('../includes/session-handler.php');
require_once FileUtils::normalizeFilePath('../includes/classes/session-manager.php');
require_once FileUtils::normalizeFilePath('../includes/classes/query-handler.php');

$conn = DatabaseConnection::connect();
$queryExecutor = new QueryExecutor($conn);

// -----------------FETCHING POSITION TITLES-----------------//
$positionQuery = "SELECT DISTINCT title FROM position";
$positionStmt = $conn->prepare($positionQuery);
$positionStmt->execute();
$positions = $positionStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$positionStmt->close();

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'candidate_creation';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';
$filter = isset($_GET['filter']) ? $_GET['filter'] : [];

// Validate sort_by and sort_order
$valid_columns = ['candidate_creation', 'first_name'];
$valid_orders = ['asc', 'desc'];

if (!in_array($sort_by, $valid_columns)) {
    $sort_by = 'candidate_creation';
}
if (!in_array(strtolower($sort_order), $valid_orders)) {
    $sort_order = 'DESC';
}

$searchCondition = $search ? "AND LOWER(CONCAT_WS(' ', COALESCE(TRIM(c.first_name), ''), COALESCE(TRIM(c.middle_name), ''), COALESCE(TRIM(c.last_name), ''), COALESCE(TRIM(c.suffix), ''), COALESCE(TRIM(p.title), ''), COALESCE(TRIM(c.candidate_creation), ''))) LIKE ?" : "";

// Prepare the filter condition
$filterCondition = "";
if (!empty($filter)) {
    $filterCondition = "AND p.title IN (" . implode(',', array_map(function ($position) use ($conn) {
        return "'" . $conn->real_escape_string($position) . "'";
    }, $filter)) . ")";
}

// Prepare the main query with sorting and filtering
$query = "SELECT c.candidate_id, c.first_name, c.middle_name, c.last_name, c.suffix, c.position_id, p.title as position, c.candidate_creation
          FROM candidate c
          JOIN position p ON c.position_id = p.position_id
          WHERE c.candidacy_status = 'Verified'
          $filterCondition
          $searchCondition
          ORDER BY $sort_by $sort_order
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);

if ($search) {
    $searchParam = "%" . strtolower($search) . "%";
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

$candidates = [];
while ($row = $result->fetch_assoc()) {
    $candidates[] = $row;
}
$stmt->close();

// Count total rows
$countQuery = "SELECT COUNT(*) as total
               FROM candidate c
               JOIN position p ON c.position_id = p.position_id
               WHERE c.candidacy_status = 'Verified'
               $filterCondition
               $searchCondition";
$countStmt = $conn->prepare($countQuery);
if ($search) {
    $countStmt->bind_param("s", $searchParam);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$countStmt->close();

echo json_encode([
    'candidates' => $candidates,
    'totalRows' => $totalRows
]);
?>
