<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Super Admin</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; margin-left: 250px; }
        .header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .btn { padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #5568d3; }
        .btn.btn-danger { background: #e74c3c; }
        .btn.btn-danger:hover { background: #c0392b; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
        .search-box { margin-bottom: 20px; display: flex; gap: 10px; }
        .search-box input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; border-bottom: 2px solid #e9ecef; }
        .table td { padding: 15px; border-bottom: 1px solid #e9ecef; }
        .table tr:hover { background: #f8f9fa; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        .sidebar-menu { position: fixed; left: 0; top: 0; width: 250px; height: 100vh; background: #2c3e50; padding: 20px 0; overflow-y: auto; z-index: 1000; }
        .sidebar-menu .logo { padding: 20px; color: white; text-align: center; border-bottom: 1px solid #34495e; margin-bottom: 20px; }
        .sidebar-menu .menu-item { display: block; padding: 12px 20px; color: #bdc3c7; text-decoration: none; transition: all 0.3s; }
        .sidebar-menu .menu-item:hover { background: #34495e; color: white; }
        .pagination { margin-top: 20px; text-align: center; }
        .pagination a, .pagination span { display: inline-block; padding: 8px 12px; margin: 0 4px; background: white; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; }
        .pagination a:hover { background: #667eea; color: white; border-color: #667eea; }
        .pagination .current { background: #667eea; color: white; border-color: #667eea; }
    </style>
</head>
<body>
    <div class="sidebar-menu">
        <div class="logo">🔐 Super Admin</div>
        <a href="/super-admin/dashboard" class="menu-item">📊 Dashboard</a>
        <a href="/super-admin/users" class="menu-item" style="background: #34495e; color: white;">👥 Users</a>
        <a href="/super-admin/access-matrix" class="menu-item">🔑 Access Matrix</a>
        <a href="/super-admin/sessions" class="menu-item">🔗 Sessions</a>
        <a href="/super-admin/audit-logs" class="menu-item">📝 Audit Logs</a>
        <a href="/super-admin/suspicious-alerts" class="menu-item">⚠️ Alerts</a>
        <a href="/super-admin/emergency-mode" class="menu-item">🚨 Emergency</a>
        <a href="/super-admin/logout" class="menu-item">🚪 Logout</a>
    </div>

    <div class="container">
        <div class="header">
            <h1>👥 Users Management</h1>
            <a href="/super-admin/create-user" class="btn">+ Add New User</a>
        </div>

        <div class="card">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by name or email...">
                <select id="roleFilter">
                    <option value="">All Roles</option>
                    <?php foreach ($data['roles'] as $role): ?>
                        <option value="<?php echo $role; ?>" <?php echo $data['filter_role'] === $role ? 'selected' : ''; ?>>
                            <?php echo ucfirst($role); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['users'])): ?>
                        <?php foreach ($data['users'] as $user): ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><code><?php echo htmlspecialchars($user['email']); ?></code></td>
                                <td><span style="background: #e8f0ff; color: #667eea; padding: 4px 8px; border-radius: 4px;"><?php echo ucfirst($user['role']); ?></span></td>
                                <td>
                                    <span class="status-badge <?php echo $user['active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $user['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never'; ?></td>
                                <td>
                                    <a href="/super-admin/edit-user/<?php echo $user['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                                    <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #999; padding: 40px;">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php 
                $pages = ceil($data['total'] / $data['limit']);
                for ($i = 1; $i <= $pages; $i++): 
                    $active = $i === $data['page'] ? 'current' : '';
                ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo $active; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.querySelector('.table tbody');
            Array.from(table.rows).forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        function deleteUser(userId) {
            if (confirm('Are you sure? This action cannot be undone.')) {
                fetch(`/super-admin/delete-user/${userId}`, { method: 'POST' })
                    .then(r => r.json())
                    .then(d => {
                        alert(d.message);
                        if (d.success) location.reload();
                    });
            }
        }
    </script>
</body>
</html>
