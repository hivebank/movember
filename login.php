<?php
require_once 'includes/config.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = apiCall('/auth/login', 'POST', [
        'email' => $email,
        'password' => $password
    ]);

    if ($result['code'] === 200 && $result['data']['success']) {
        $_SESSION['user_id'] = $result['data']['user']['id'];
        $_SESSION['token'] = $result['data']['token'];
        $_SESSION['user'] = $result['data']['user'];

        header('Location: /dashboard.php');
        exit;
    } else {
        $error = $result['data']['message'] ?? 'Login failed. Please try again.';
    }
}

$pageTitle = 'Login - FormFlow';
include 'includes/header.php';
?>

<main>
    <div class="form-container">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1>Sign in to your account</h1>
            <p style="margin-top: 0.5rem; color: #6b7280;">
                Or <a href="/register.php" style="color: #0ea5e9;">create a new account</a>
            </p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required placeholder="you@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Sign in</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
