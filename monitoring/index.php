<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQL Active Users Monitor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">📊 Active MySQL Users Monitor</h2>
    
    <div class="card">
        <div class="card-body">
            <h5>Active Users Executing Queries: <span id="userCount" class="badge bg-primary">0</span></h5>
            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Host</th>
                        <th>Time (s)</th>
                        <th>State</th>
                        <th>Query</th>
                    </tr>
                </thead>
                <tbody id="userTable"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
function fetchUsers() {
    $.ajax({
        url: "fetch_users.php",
        method: "GET",
        dataType: "json",
        success: function(data) {
            $("#userCount").text(data.active_users);
            let rows = "";
            data.users.forEach((user, index) => {
                rows += `<tr>
                            <td>${index + 1}</td>
                            <td>${user.user}</td>
                            <td>${user.host}</td>
                            <td>${user.time}</td>
                            <td>${user.state}</td>
                            <td>${user.info || 'N/A'}</td>
                         </tr>`;
            });
            $("#userTable").html(rows);
        }
    });
}

// Auto-refresh every 5 seconds
setInterval(fetchUsers, 5000);
fetchUsers();
</script>

</body>
</html>
