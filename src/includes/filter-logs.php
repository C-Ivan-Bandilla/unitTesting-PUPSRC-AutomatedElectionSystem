<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'classes/file-utils.php');
require_once FileUtils::normalizeFilePath('classes/db-connector.php');
require_once FileUtils::normalizeFilePath('session-handler.php');
require_once FileUtils::normalizeFilePath('error-reporting.php');
include_once FileUtils::normalizeFilePath('default-time-zone.php');
include_once FileUtils::normalizeFilePath('constants.php');

if (isset($_POST['filter'])) {
    $filter = $_POST['filter'];
    $voter_id = $_SESSION['voter_id'];
    $role = $_SESSION['role'];

    $head_admin_email = '';

    $connection = DatabaseConnection::connect();
    $sql = "SELECT al.timestamp, al.ip_address, al.browser, v.email, v.role, v.first_name, al.action
            FROM activity_log al
            JOIN voter v ON al.voter_id = v.voter_id";

    if ($role !== ROLE_HEAD_ADMIN) {
        $sql .= " WHERE al.voter_id = ?";
    }

    if ($filter === 'adminActLogs') {
        $sql .= ($role !== ROLE_HEAD_ADMIN) ? " AND v.role = 'admin'" : " WHERE v.role = 'admin'";
    } elseif ($filter === 'voterActLogs') {
        $sql .= ($role !== ROLE_HEAD_ADMIN) ? " AND v.role = 'student_voter'" : " WHERE v.role = 'student_voter'";
    }

    $sql .= " ORDER BY al.timestamp DESC";
    $stmt = $connection->prepare($sql);

    if ($role !== ROLE_HEAD_ADMIN) {
        $stmt->bind_param('i', $voter_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $current_date = '';

    ob_start();
    
    while ($row = $result->fetch_assoc()) {
        $formatted_date = date('F j, Y', strtotime($row['timestamp']));
        $formatted_time = date('g:i A', strtotime($row['timestamp']));

        switch ($row['role']) {
            case 'student_voter':
                $formatted_role = 'Student Voter';
                break;
            case 'admin':
                $formatted_role = 'Admin';
                break;
            case 'head_admin':
                $formatted_role = 'Head Admin';
                break;
        }

        if ($row['role'] === ROLE_HEAD_ADMIN) {
            $action = $HEAD_ADMIN_ACTIONS[$row['action']] ?? $row['action'];
            $head_admin_email = $row['email'];
        } else {
            if ($row['role'] === ROLE_ADMIN) {
                $actions = ADMIN_ACTIONS;
            } else {
                $actions = STUDENT_VOTER_ACTIONS;
            }
            $action = $actions[$row['action']] ?? $row['action'];

            if ($role === ROLE_HEAD_ADMIN) {
                $action = str_replace(
                    ['You', 'your'],
                    [htmlspecialchars($row['first_name']) . ' (' . htmlspecialchars($formatted_role) . ') ', 'his'],
                    $action
                );
            }
        }

        if ($formatted_date !== $current_date) {
            if ($current_date !== '') {
                echo '</ul></div></div></div>';
            }
            $current_date = $formatted_date;

            echo '<div class="col-12 mt-4">';
            echo '<div class="card border border-0 rounded-3">';
            echo '<div class="card-body">';
            echo '<div class="card-title py-3">' . htmlspecialchars($formatted_date) . '</div>';
            echo '<ul class="timeline">';
        }

        echo '<li>';
        echo '<div class="row pb-4">';
        echo '<div class="col-2">';
        echo '<div class="time text-secondary">' . htmlspecialchars($formatted_time) . '</div>';
        echo '</div>';
        echo '<div class="col-10 activity-content">';
        echo '<div class="activity-title">' . $action . '</div>';
        echo '<ul class="list-inline text-secondary">';

        if ($role === ROLE_HEAD_ADMIN && $row['email'] !== $head_admin_email) {
            echo '<li class="list-inline-item activity-info">Email: ' . htmlspecialchars($row['email']) . '</li>';
        }

        echo '<li class="list-inline-item activity-info">IP Address: ' . htmlspecialchars($row['ip_address']) . '</li>';
        echo '<li class="list-inline-item activity-info">Browser: ' . htmlspecialchars($row['browser']) . '</li>';
        echo '</ul></div></div></li>';
    }

    echo '</ul></div></div></div>';

    $stmt->close();
    $connection->close();
    echo ob_get_clean();
}