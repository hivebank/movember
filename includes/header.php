<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'FormFlow - Modern Form Builder'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Tailwind CSS for modern styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="/" class="logo">FormFlow</a>
                <ul class="nav-links">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/dashboard.php">Dashboard</a></li>
                        <li><a href="/pricing.php">Pricing</a></li>
                        <li><a href="/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="/pricing.php">Pricing</a></li>
                        <li><a href="/login.php">Login</a></li>
                        <li><a href="/register.php" class="btn btn-primary">Get Started</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
