<?php

$conn = DatabaseConnection::connect();

// Initialize the SQL query
$query = "SELECT * FROM candidate WHERE candidacy_status = 'Verified'";

// Prepare the statement
$stmt = $conn->prepare($query);
$stmt->execute();
$verified_tbl = $stmt->get_result();

$stmt->close();
$conn->close();
?>
