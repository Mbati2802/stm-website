<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - Super Admin</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; margin-left: 250px; }
        .header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
        .filters { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .filters input, .filters select { padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e9ecef; }
        .table td { padding: 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; }
        .table tr:hover { background: #f8f9fa; }
        .status-success { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; display: inline-block; }
        .status-failed { background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; display: inline-block; }
        .sidebar-menu { position: fixed; left: 0; top: 0; width: 250px; height: 100vh; background: #2c3e50; padding: 20px 0; overflow-y: auto; z-index: 1000; }
        .sidebar-menu .menu-item { display: block; padding: 12px 20px; color: #bdc3c7; text-decoration: none; }
        .sidebar-menu .menu-item:hover { background: #34495e; color: white; }
        .btn { padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class="sidebar-menu">
        <div class="logo" style="padding: 20px; color: white; text-align: center; border-bottom: 1px solid #34495e; margin-bottom: 20px;">🔐 Super Admin</div>
        <a href="/super-admin/dashboard" class="menu-item">📊 Dashboard</a>
        <a href="/super-admin/users" class="menu-item">👥 Users</a>
        <a href="/super-admin/access-matrix" class="menu-item">🔑 Access Matrix</a>
        <a href="/super-admin/sessions" class="menu-item">🔗 Sessions</a>
        <a href="/super-admin/audit-logs" class="menu-item" style="background: #34495e; color: white;">📝 Audit Logs</a>
        <a href="/super-admin/suspicious-alerts" class="menu-item">⚠️ Alerts</a>
        <a href="/super-admin/emergency-mode" class="menu-item">🚨 Emergency</a>
        <a href="/super-admin/logout" class="menu-item">🚪 Logout</a>
    </div>

    <div class="container">
        <div class="header">
            <h1>📝 Audit Logs</h1>
            <p style="color: #999; margin-top: 5px;">View all system activities and changes</p>
        </div>

        <div class="card">
            <div class="filters">
                <select id="userTypeFilter" onchange="applyFilters()">
                    <option value="">All User Types</option>
                    <option value="admin">Admin</option>
                    <option value="crm_admin">CRM Admin</option>
                    <option value="student">Student</option>
                    <option value="super_admin">Super Admin</option>
                </select>
                <select id="actionFilter" onchange="applyFilters()">
                    <option value="">All Actions</option>
                    <option value="create">Create</option>
                    <option value="edit">Edit</option>
                    <option value="delete">Delete</option>
                    <option value="login">Login</option>
                    <option value="export">Export</option>
                </select>
                <select id="statusFilter" onchange="applyFilters()">
                    <option value="">All Statuses</option>
                    <option value="success">Success</option>
                    <option value="failed">Failed</option>
                    <option value="suspicious">Suspicious</option>
                </select>
                <input type="date" id="startDate" onchange="applyFilters()">
                <input type="date" id="endDate" onchange="applyFilters()">
                <button class="btn" onclick="exportLogs()">📥 Export</button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User Type</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['logs'])): ?>
                        <?php foreach ($data['logs'] as $log): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i:s', strtotime($log['timestamp'])); ?></td>
                                <td><?php echo ucfirst($log['user_type']); ?></td>
                                <td><?php echo ucfirst($log['action']); ?></td>
                                <td><?php echo $log['entity_type']; ?></td>
                                <td><code><?php echo $log['ip_address']; ?></code></td>
                                <td><span class="status-<?php echo $log['status']; ?>"><?php echo ucfirst($log['status']); ?></span></td>
                                <td>
                                    <button class="btn" style="padding: 4px 8px; font-size: 11px;" onclick="showDetails(<?php echo htmlspecialchars(json_encode($log)); ?>)">View</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #999; padding: 40px;">No logs found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div style="margin-top: 20px; text-align: center; color: #999;">
                Showing <?php echo count($data['logs'] ?? []); ?> of <?php echo $data['total'] ?? 0; ?> logs
            </div>
        </div>
    </div>

    <script>
        function applyFilters() {
            const userType = document.getElementById('userTypeFilter').value;
            const action = document.getElementById('actionFilter').value;
            const status = document.getElementById('statusFilter').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            let url = '/super-admin/audit-logs?';
            if (userType) url += `user_type=${userType}&`;
            if (action) url += `action=${action}&`;
            if (status) url += `status=${status}&`;
            if (startDate) url += `start_date=${startDate}&`;
            if (endDate) url += `end_date=${endDate}&`;

            window.location.href = url;
        }

        function showDetails(log) {
            alert(`Action: ${log.action}\nEntity: ${log.entity_type}\nUser: ${log.user_id}\nIP: ${log.ip_address}\nTime: ${log.timestamp}\n\nOld Values: ${JSON.stringify(log.old_values)}\n\nNew Values: ${JSON.stringify(log.new_values)}`);
        }

        function exportLogs() {
            alert('Export functionality coming soon');
        }
    </script>
</body>
</html>
