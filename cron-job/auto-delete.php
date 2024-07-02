<?php
// Database configurations
$databases = array(
    'ACAP' => array(
        'host' => 'localhost',
        'database' => 'u155023598_db_acap',
        'username' => 'u155023598_acap',
        'password' => 'Student_0rg' // Replace with actual password
    ),
    'AECES' => array(
        'host' => 'localhost',
        'database' => 'u155023598_db_aeces',
        'username' => 'u155023598_aeces',
        'password' => 'Student_0rg'
    ),
    'ELITE' => array(
        'host' => 'localhost',
        'database' => 'u155023598_db_elite',
        'username' => 'u155023598_elite',
        'password' => 'Student_0rg'
    ),
    'GIVE' => array(
        'host' => 'localhost',
        'database' => 'u155023598_db_give',
        'username' => 'u155023598_give',
        'password' => 'Student_0rg'
    ),
    'JEHRA' => array(
        'host' => 'localhost',
        'database' => 'u155023598_db_jehra',
        'username' => 'u155023598_jehra',
        'password' => 'Student_0rg'
    ),
    'JMAP' => array(
        'host' => 'localhost',
        'database' => 'u155023598_db_jmap',
        'username' => 'u155023598_jmap',
        'password' => 'Student_0rg'
    ),
    'JPIA' => array(
        'host' => 'localhost',
        'database' => 'u155023598_db_jpia',
        'username' => 'u155023598_jpia',
        'password' => 'Student_0rg'
    ),
    'PIIE' => array(
        'host' => 'localhost',
        'database' => 'u155023598_db_piie',
        'username' => 'u155023598_piie',
        'password' => 'Student_0rg'
    ),
    'SCO' => array(
        'host' => 'localhost',
        'database' => 'u155023598_db_sco',
        'username' => 'u155023598_sco',
        'password' => 'Student_0rg'
    )
);

try {
    // Loop through each database configuration
    foreach ($databases as $name => $config) {
        // Establish database connection using PDO
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8";
        $pdo = new PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Delete invalid voters
        $sqlVoter = "DELETE FROM voter 
                     WHERE account_status = 'invalid' 
                     AND status_updated < NOW() - INTERVAL 30 DAY";
        
        $stmtVoter = $pdo->prepare($sqlVoter);
        $stmtVoter->execute();
        
        // Delete invalid candidates
        $sqlCandidate = "DELETE FROM candidate 
                         WHERE candidacy_status = 'invalid' 
                         AND status_updated < NOW() - INTERVAL 30 DAY";
        
        $stmtCandidate = $pdo->prepare($sqlCandidate);
        $stmtCandidate->execute();
        
        // Output success message
        echo "Deleted invalid voters and candidates from $name database successfully.<br>";
    }
} catch (PDOException $e) {
    echo "Error deleting invalid records: " . $e->getMessage();
}
?>
