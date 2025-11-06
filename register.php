<?php
require_once 'includes/config.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $company = $_POST['company'] ?? '';
    $password = $_POST['password'] ?? '';

    if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        $result = apiCall('/auth/register', 'POST', [
            'name' => $name,
            'email' => $email,
            'company' => $company,
            'password' => $password
        ]);

        if ($result['code'] === 201 && $result['data']['success']) {
            $_SESSION['user_id'] = $result['data']['user']['id'];
            $_SESSION['token'] = $result['data']['token'];
            $_SESSION['user'] = $result['data']['user'];

            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['data']['message'] ?? 'Registration failed. Please try again.';
        }
    }
}

$pageTitle = 'Register - FormFlow';
include 'includes/header.php';
?>

<main>
    <div class="form-container">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1>Create your account</h1>
            <p style="margin-top: 0.5rem; color: #6b7280;">
                Or <a href="login.php" style="color: #0ea5e9;">sign in to existing account</a>
            </p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="John Doe">
            </div>

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required placeholder="you@example.com">
            </div>

            <div class="form-group">
                <label for="company">Company (optional)</label>
                <input type="text" id="company" name="company" placeholder="Your Company">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8" placeholder="••••••••">
                <small style="color: #6b7280;">Must be at least 8 characters</small>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Create account</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
