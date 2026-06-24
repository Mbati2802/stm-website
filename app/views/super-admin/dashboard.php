<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #5568d3;
        }

        .btn.btn-danger {
            background: #e74c3c;
        }

        .btn.btn-danger:hover {
            background: #c0392b;
        }

        .btn.btn-warning {
            background: #f39c12;
        }

        .btn.btn-warning:hover {
            background: #d68910;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card .icon {
            font-size: 40px;
            opacity: 0.8;
        }

        .stat-card .content {
            flex: 1;
            margin-left: 15px;
        }

        .stat-card .label {
            color: #999;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }

        .alert-badge {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 10px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            margin-top: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e9ecef;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
        }

        .status-warning {
            background: #fff3cd;
            color: #856404;
        }

        .status-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .sidebar-menu {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #2c3e50;
            padding: 20px 0;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-menu .logo {
            padding: 20px;
            color: white;
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            border-bottom: 1px solid #34495e;
            margin-bottom: 20px;
        }

        .sidebar-menu .menu-item {
            display: block;
            padding: 12px 20px;
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu .menu-item:hover,
        .sidebar-menu .menu-item.active {
            background: #34495e;
            color: white;
            border-left-color: #667eea;
        }

        .main-content {
            margin-left: 250px;
        }

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .alert-item {
            background: #fff3cd;
            border-left: 4px solid #f39c12;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .alert-item.critical {
            background: #f8d7da;
            border-left-color: #e74c3c;
        }

        .alert-item.info {
            background: #d1ecf1;
            border-left-color: #17a2b8;
        }
    </style>
</head>
<body>
    <div class="sidebar-menu">
        <div class="logo">🔐 Super Admin</div>
        <a href="/super-admin/dashboard" class="menu-item active">📊 Dashboard</a>
        <a href="/super-admin/users" class="menu-item">👥 Users</a>
        <a href="/super-admin/access-matrix" class="menu-item">🔑 Access Matrix</a>
        <a href="/super-admin/sessions" class="menu-item">🔗 Sessions</a>
        <a href="/super-admin/audit-logs" class="menu-item">📝 Audit Logs</a>
        <a href="/super-admin/suspicious-alerts" class="menu-item">⚠️ Alerts</a>
        <a href="/super-admin/emergency-mode" class="menu-item">🚨 Emergency</a>
        <a href="/super-admin/logout" class="menu-item">🚪 Logout</a>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="header">
                <div>
                    <h1>🎛️ Control Center</h1>
                    <p style="color: #999; margin-top: 5px;">Welcome, Super Administrator</p>
                </div>
                <div class="header-actions">
                    <a href="/super-admin/users" class="btn">+ Add User</a>
                    <a href="/super-admin/sessions" class="btn">Manage Sessions</a>
                </div>
            </div>

            <div class="grid">
                <div class="card stat-card">
                    <div class="icon">👥</div>
                    <div class="content">
                        <div class="label">Admin Users</div>
                        <div class="value"><?php echo $data['admin_count'] ?? 0; ?></div>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="icon">🎓</div>
                    <div class="content">
                        <div class="label">Students</div>
                        <div class="value"><?php echo $data['student_count'] ?? 0; ?></div>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="icon">🔗</div>
                    <div class="content">
                        <div class="label">Active Sessions</div>
                        <div class="value"><?php echo count($data['active_sessions'] ?? []); ?></div>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="icon">📝</div>
                    <div class="content">
                        <div class="label">Audit Logs</div>
                        <div class="value"><?php echo $data['activity_stats']['period_days'] ?? 7; ?>d</div>
                    </div>
                </div>
            </div>

            <div class="section-title">
                ⚠️ Suspicious Activity <span class="alert-badge"><?php echo count($data['suspicious_alerts'] ?? []); ?></span>
            </div>
            <div class="card">
                <?php if (!empty($data['suspicious_alerts'])): ?>
                    <?php foreach (array_slice($data['suspicious_alerts'], 0, 5) as $alert): ?>
                        <div class="alert-item <?php echo $alert['severity']; ?>">
                            <strong><?php echo ucfirst($alert['alert_type']); ?></strong> - <?php echo $alert['description']; ?>
                            <div style="font-size: 12px; color: #666; margin-top: 5px;">
                                <?php echo date('M d, Y H:i', strtotime($alert['created_at'])); ?> | IP: <?php echo $alert['ip_address']; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #999; text-align: center; padding: 20px;">No suspicious activities detected</p>
                <?php endif; ?>
            </div>

            <div class="section-title">📊 Activity Statistics (Last 7 Days)</div>
            <div class="grid">
                <?php if (!empty($data['activity_stats']['action_breakdown'])): ?>
                    <?php foreach (array_slice($data['activity_stats']['action_breakdown'], 0, 4) as $stat): ?>
                        <div class="card stat-card">
                            <div class="icon">📈</div>
                            <div class="content">
                                <div class="label"><?php echo ucfirst($stat['action']); ?></div>
                                <div class="value"><?php echo $stat['count']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="section-title">🔗 Active Sessions</div>
            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>User Type</th>
                            <th>IP Address</th>
                            <th>Last Activity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['active_sessions'])): ?>
                            <?php foreach (array_slice($data['active_sessions'], 0, 10) as $session): ?>
                                <tr>
                                    <td>#<?php echo $session['user_id']; ?></td>
                                    <td><span class="status-badge status-success"><?php echo $session['user_type']; ?></span></td>
                                    <td><code><?php echo $session['ip_address']; ?></code></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($session['last_activity'])); ?></td>
                                    <td><span class="status-badge status-success">Active</span></td>
                                    <td>
                                        <button onclick="forceLogout(<?php echo $session['id']; ?>)" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;">Logout</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #999; padding: 20px;">No active sessions</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="section-title">📝 Recent Activity</div>
            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>User Type</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['recent_activity'])): ?>
                            <?php foreach ($data['recent_activity'] as $log): ?>
                                <tr>
                                    <td><?php echo date('M d, Y H:i:s', strtotime($log['timestamp'])); ?></td>
                                    <td><span class="status-badge status-success"><?php echo $log['user_type']; ?></span></td>
                                    <td><?php echo ucfirst($log['action']); ?></td>
                                    <td><?php echo $log['entity_type']; ?></td>
                                    <td><code><?php echo $log['ip_address']; ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #999; padding: 20px;">No recent activity</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function forceLogout(sessionId) {
            if (confirm('Are you sure you want to force logout this user?')) {
                fetch(`/super-admin/force-logout/${sessionId}`, { method: 'POST' })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            alert('User logged out');
                            location.reload();
                        } else {
                            alert('Error: ' + d.message);
                        }
                    });
            }
        }

        // Auto-refresh dashboard every 30 seconds
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
