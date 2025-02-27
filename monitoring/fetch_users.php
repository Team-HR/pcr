<?php
require 'config.php';

$sql = "SHOW PROCESSLIST";
$result = $conn->query($sql);

$active_users = 0;
$users = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['db'] == $dbname) { // Filter by your database
            $active_users++;
            $users[] = [
                'id' => $row['Id'],
                'user' => $row['User'],
                'host' => $row['Host'],
                'time' => $row['Time'],
                'state' => $row['State'],
                'info' => $row['Info']
            ];
        }
    }
}

$response = [
    'active_users' => $active_users,
    'users' => $users
];

echo json_encode($response);
$conn->close();
?>
