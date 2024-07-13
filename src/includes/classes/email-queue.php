<?php

class EmailQueue
{
    private static $connection;
    private static $status = 'pending';

    public static function generateHash()
    {
        return hash('sha256', bin2hex(random_bytes(16)) . date('Y-m-d H:i'));
    }

    private static function ensureConnection()
    {
        if (self::$connection === null) {
            self::$connection = DatabaseConnection::connect();
        }
    }

    public static function insertQueue($emails, $schedule, $hash)
    {
        self::ensureConnection();

        if (self::$connection) {

            foreach ($emails as $email) {
                $sql = "INSERT INTO email_queue (status, schedule, push_id, content) VALUES (?, ?, ?, ?)";
                $stmt = self::$connection->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("ssss", self::$status, $schedule, $hash, $email);
                    $stmt->execute();

                    if ($stmt->affected_rows <= 0) {
                        // No rows were affected
                    }

                    $stmt->close();
                } else {
                    echo "Error preparing statement: " . self::$connection->error;
                }
            }
        }
    }

    public static function getCurrQueue()
    {
        self::ensureConnection();
        $emails = [];

        if (self::$connection) {
            $currentDatetime = date('Y-m-d H:i:s');

            $sql = "SELECT status, schedule, content, push_id FROM email_queue WHERE schedule <= ?";
            $stmt = self::$connection->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('s', $currentDatetime);
                $stmt->execute();
                $status =  $schedule = $push_id = $content = '';
                $stmt->bind_result($status, $schedule, $content, $push_id);

                while ($stmt->fetch()) {
                    $emails[] = [
                        'status' => $status,
                        'schedule' => $schedule,
                        'content' => $content,
                        'push_id' => $push_id
                    ];
                }

                $stmt->close();
            } else {
                echo "Error preparing statement: " . self::$connection->error;
            }
        }

        return $emails;
    }

    protected static function deleteQueues($push_id)
    {
        self::ensureConnection();

        if (self::$connection) {

            $sql = "DELETE FROM email_queue WHERE push_id = ?";
            $stmt = self::$connection->prepare($sql);

            if ($stmt) {
                $stmt->bind_param('i', $push_id); // Use 'i' for integer value
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo "Email with push_id: $push_id deleted successfully.";
                } else {
                    echo "No email found with push_id: $push_id.";
                }

                $stmt->close();
            } else {
                echo "Error preparing statement: " . self::$connection->error;
            }
        }
    }

    protected static function deleteQueue($schedule = null, $email_id = null)
    {
        self::ensureConnection();

        if (self::$connection) {
            $sql = "DELETE FROM email_queue WHERE ";
            $params = [];

            if ($schedule !== null) {
                $sql .= "schedule = ?";
                $params[] = $schedule;
            }

            if ($email_id !== null) {
                if (isset($params[0])) {
                    $sql .= " OR "; // Add OR if both schedule and email_id are provided
                }
                $sql .= "email_id = ?";
                $params[] = $email_id;
            }

            if (empty($params)) {
                echo "Error: No deletion criteria provided (schedule or email_id).";
                return;
            }

            $stmt = self::$connection->prepare($sql);

            if ($stmt) {
                $stmt->bind_param(str_repeat('i', count($params)), ...$params);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $deletedCount = $stmt->affected_rows;
                    echo "Successfully deleted $deletedCount emails.";
                } else {
                    echo "No emails found matching the criteria.";
                }

                $stmt->close();
            } else {
                echo "Error preparing statement: " . self::$connection->error;
            }
        }
    }
}
