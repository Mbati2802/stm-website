<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $metaTitle ?? 'CRM' ?> - St. Mary's MCH Medical Training College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #185490;
            --secondary: #00aae8;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .crm-navbar {
            background: var(--primary);
            color: white;
            padding: 15px 0;
        }
        .crm-navbar .navbar-brand {
            color: white;
            font-weight: bold;
        }
        .crm-navbar .nav-link {
            color: rgba(255,255,255,0.8);
        }
        .crm-navbar .nav-link:hover,
        .crm-navbar .nav-link.active {
            color: white;
        }
        .crm-sidebar {
            background: white;
            min-height: calc(100vh - 60px);
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }
        .crm-sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            border-radius: 0;
            border-left: 3px solid transparent;
        }
        .crm-sidebar .nav-link:hover,
        .crm-sidebar .nav-link.active {
            background: #f8f9fa;
            border-left-color: var(--primary);
            color: var(--primary);
        }
        .crm-sidebar .nav-link i {
            margin-right: 10px;
        }
        .crm-content {
            padding: 20px;
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .card-header {
            background: white;
            border-bottom: 2px solid var(--primary);
            font-weight: bold;
        }
        .metric-card {
            text-align: center;
            padding: 20px;
        }
        .metric-card .metric-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
        }
        .metric-card .metric-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo img {
            width: 80px;
            height: 80px;
        }
        .login-logo h3 {
            color: var(--primary);
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php if (CRMAuth::check()): ?>
    <nav class="navbar crm-navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="/crm/dashboard">
                <i class="bi bi-diagram-3"></i> CRM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="bi bi-person-circle"></i> <?= CRMAuth::username() ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/crm/logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 crm-sidebar">
                <nav class="nav flex-column">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'crm/dashboard') ? 'active' : '' ?>" href="/crm/dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'crm/leads') ? 'active' : '' ?>" href="/crm/leads">
                        <i class="bi bi-people"></i> Leads
                    </a>
                    <?php if (CRMAuth::isAdmin()): ?>
                    <a class="nav-link" href="#">
                        <i class="bi bi-person-gear"></i> Users
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
            <div class="col-md-10 crm-content">
                <?php require_once $viewPath; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
        <?php require_once $viewPath; ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
