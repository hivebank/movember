<?php
require_once 'includes/config.php';
$pageTitle = 'Pricing - FormFlow';
include 'includes/header.php';
?>

<main>
    <div class="container" style="padding: 4rem 0;">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h1 style="margin-bottom: 1rem;">Simple, Transparent Pricing</h1>
            <p style="font-size: 1.25rem; color: #6b7280;">Choose the plan that's right for you</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; max-width: 1000px; margin: 0 auto;">
            <!-- Free Plan -->
            <div class="card" style="border: 2px solid #e5e7eb;">
                <h3 style="margin-bottom: 0.5rem;">Free</h3>
                <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 1rem;">$0<span style="font-size: 1rem; color: #6b7280;">/mo</span></div>
                <ul style="list-style: none; margin-bottom: 1.5rem;">
                    <li style="margin-bottom: 0.75rem;">✓ 3 forms</li>
                    <li style="margin-bottom: 0.75rem;">✓ 100 responses/month</li>
                    <li style="margin-bottom: 0.75rem; color: #9ca3af;">✗ File uploads</li>
                    <li style="margin-bottom: 0.75rem; color: #9ca3af;">✗ Payment collection</li>
                </ul>
                <?php if (!isLoggedIn()): ?>
                    <a href="/register.php" class="btn btn-outline" style="width: 100%; text-align: center;">Get Started</a>
                <?php else: ?>
                    <button disabled class="btn btn-secondary" style="width: 100%;">Current Plan</button>
                <?php endif; ?>
            </div>

            <!-- Starter Plan -->
            <div class="card" style="border: 2px solid #0ea5e9;">
                <h3 style="margin-bottom: 0.5rem;">Starter</h3>
                <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 1rem;">$19<span style="font-size: 1rem; color: #6b7280;">/mo</span></div>
                <ul style="list-style: none; margin-bottom: 1.5rem;">
                    <li style="margin-bottom: 0.75rem;">✓ 25 forms</li>
                    <li style="margin-bottom: 0.75rem;">✓ 1,000 responses/month</li>
                    <li style="margin-bottom: 0.75rem;">✓ File uploads</li>
                    <li style="margin-bottom: 0.75rem;">✓ Payment collection</li>
                </ul>
                <button class="btn btn-primary" style="width: 100%;">Subscribe</button>
            </div>

            <!-- Pro Plan -->
            <div class="card" style="border: 2px solid #0ea5e9; background: #eff6ff;">
                <div style="background: #0ea5e9; color: white; font-size: 0.875rem; font-weight: 600; padding: 0.25rem 0.75rem; border-radius: 9999px; display: inline-block; margin-bottom: 0.5rem;">
                    Popular
                </div>
                <h3 style="margin-bottom: 0.5rem;">Pro</h3>
                <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 1rem;">$49<span style="font-size: 1rem; color: #6b7280;">/mo</span></div>
                <ul style="list-style: none; margin-bottom: 1.5rem;">
                    <li style="margin-bottom: 0.75rem;">✓ Unlimited forms</li>
                    <li style="margin-bottom: 0.75rem;">✓ 10,000 responses/month</li>
                    <li style="margin-bottom: 0.75rem;">✓ Custom branding</li>
                    <li style="margin-bottom: 0.75rem;">✓ Advanced analytics</li>
                    <li style="margin-bottom: 0.75rem;">✓ API access</li>
                </ul>
                <button class="btn btn-primary" style="width: 100%;">Subscribe</button>
            </div>

            <!-- Enterprise Plan -->
            <div class="card" style="border: 2px solid #e5e7eb;">
                <h3 style="margin-bottom: 0.5rem;">Enterprise</h3>
                <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 1rem;">$199<span style="font-size: 1rem; color: #6b7280;">/mo</span></div>
                <ul style="list-style: none; margin-bottom: 1.5rem;">
                    <li style="margin-bottom: 0.75rem;">✓ Everything in Pro</li>
                    <li style="margin-bottom: 0.75rem;">✓ Unlimited responses</li>
                    <li style="margin-bottom: 0.75rem;">✓ Team collaboration</li>
                    <li style="margin-bottom: 0.75rem;">✓ Priority support</li>
                </ul>
                <button class="btn btn-primary" style="width: 100%;">Subscribe</button>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
