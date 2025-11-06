<?php
require_once 'includes/config.php';
$pageTitle = 'FormFlow - Create Beautiful Forms in Minutes';
include 'includes/header.php';
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Create Beautiful Forms in Minutes</h1>
            <p>Build step-by-step forms that convert. Collect payments, analyze responses, and grow your business with FormFlow.</p>
            <div class="hero-buttons">
                <a href="/register.php" class="btn btn-primary">Get Started Free</a>
                <a href="/pricing.php" class="btn btn-outline">View Pricing</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2>Why Choose FormFlow?</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <h3>Beautiful Design</h3>
                    <p>Create stunning, professional forms with our intuitive drag-and-drop builder.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">💳</div>
                    <h3>Accept Payments</h3>
                    <p>Collect payments seamlessly with integrated Stripe support.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Analytics</h3>
                    <p>Track responses and gain insights with powerful analytics tools.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Mobile Responsive</h3>
                    <p>Forms look perfect on all devices - desktop, tablet, and mobile.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Secure</h3>
                    <p>Enterprise-grade security to protect your data and your users.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Fast & Reliable</h3>
                    <p>Lightning-fast form loading and 99.9% uptime guarantee.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
