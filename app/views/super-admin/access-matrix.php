<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Matrix - Super Admin</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; margin-left: 250px; }
        .header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 20px; overflow-x: auto; }
        .matrix-table { width: 100%; border-collapse: collapse; }
        .matrix-table th, .matrix-table td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        .matrix-table th { background: #667eea; color: white; font-weight: 600; }
        .matrix-table tr:nth-child(even) { background: #f8f9fa; }
        .resource-col { background: #f0f0f0; font-weight: 600; text-align: left; }
        .checkbox { width: 20px; height: 20px; cursor: pointer; }
        .btn { padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #5568d3; }
        .btn.btn-success { background: #27ae60; }
        .btn.btn-success:hover { background: #229954; }
        .sidebar-menu { position: fixed; left: 0; top: 0; width: 250px; height: 100vh; background: #2c3e50; padding: 20px 0; overflow-y: auto; z-index: 1000; }
        .sidebar-menu .menu-item { display: block; padding: 12px 20px; color: #bdc3c7; text-decoration: none; }
        .sidebar-menu .menu-item:hover { background: #34495e; color: white; }
        .legend { display: flex; gap: 20px; margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .legend-item { display: flex; align-items: center; gap: 8px; }
        .legend-check { width: 20px; height: 20px; background: #27ae60; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="sidebar-menu">
        <div class="logo" style="padding: 20px; color: white; text-align: center; border-bottom: 1px solid #34495e; margin-bottom: 20px;">🔐 Super Admin</div>
        <a href="/super-admin/dashboard" class="menu-item">📊 Dashboard</a>
        <a href="/super-admin/users" class="menu-item">👥 Users</a>
        <a href="/super-admin/access-matrix" class="menu-item" style="background: #34495e; color: white;">🔑 Access Matrix</a>
        <a href="/super-admin/sessions" class="menu-item">🔗 Sessions</a>
        <a href="/super-admin/audit-logs" class="menu-item">📝 Audit Logs</a>
        <a href="/super-admin/suspicious-alerts" class="menu-item">⚠️ Alerts</a>
        <a href="/super-admin/emergency-mode" class="menu-item">🚨 Emergency</a>
        <a href="/super-admin/logout" class="menu-item">🚪 Logout</a>
    </div>

    <div class="container">
        <div class="header">
            <h1>🔑 Access Matrix</h1>
            <button class="btn btn-success" onclick="saveChanges()">💾 Save Changes</button>
        </div>

        <div class="card">
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-check"></div>
                    <span>✓ = Permission Granted</span>
                </div>
                <div class="legend-item">
                    <span>□ = Permission Denied</span>
                </div>
            </div>

            <table class="matrix-table" id="matrixTable">
                <thead>
                    <tr>
                        <th>Resource / Role</th>
                        <?php 
                        if (!empty($data['matrix'])) {
                            foreach (array_keys($data['matrix']) as $role):
                        ?>
                            <th><?php echo ucfirst($role); ?></th>
                        <?php 
                            endforeach;
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($data['resources'])) {
                        foreach ($data['resources'] as $resource):
                    ?>
                        <tr>
                            <td class="resource-col"><?php echo ucfirst($resource); ?></td>
                            <?php 
                            foreach (array_keys($data['matrix'] ?? []) as $role):
                                $hasPermission = $data['matrix'][$role][$resource]['view'] ?? 0;
                            ?>
                                <td>
                                    <input type="checkbox" class="checkbox" data-role="<?php echo $role; ?>" data-resource="<?php echo $resource; ?>" <?php echo $hasPermission ? 'checked' : ''; ?>>
                                </td>
                            <?php 
                            endforeach;
                            ?>
                        </tr>
                    <?php 
                        endforeach;
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>📋 Instructions</h3>
            <ul style="line-height: 1.8; color: #666;">
                <li><strong>Check a box</strong> to grant permission for a role to view a resource</li>
                <li><strong>Uncheck a box</strong> to revoke permission</li>
                <li><strong>Click "Save Changes"</strong> to apply all modifications</li>
                <li>Changes are logged in the audit trail for compliance</li>
                <li>Super Admin always has full access regardless of matrix settings</li>
            </ul>
        </div>
    </div>

    <script>
        function saveChanges() {
            const matrix = {};
            const checkboxes = document.querySelectorAll('.checkbox');

            checkboxes.forEach(cb => {
                const role = cb.dataset.role;
                const resource = cb.dataset.resource;
                const granted = cb.checked ? 1 : 0;

                if (!matrix[role]) matrix[role] = {};
                if (!matrix[role][resource]) matrix[role][resource] = {};
                matrix[role][resource]['view'] = granted;
            });

            const formData = new FormData();
            formData.append('matrix_data', JSON.stringify(matrix));

            fetch('/super-admin/update-access-matrix', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(d => {
                alert(d.message);
                if (d.success) {
                    location.reload();
                }
            })
            .catch(e => {
                alert('Error: ' + e.message);
            });
        }

        // Auto-save on individual checkbox change (optional)
        document.querySelectorAll('.checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                console.log(`Permission changed: ${this.dataset.role} - ${this.dataset.resource}`);
            });
        });
    </script>
</body>
</html>
