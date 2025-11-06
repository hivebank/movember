<?php
require_once 'includes/config.php';
requireLogin();

$pageTitle = 'Dashboard - FormFlow';
include 'includes/header.php';

// Fetch forms via API
$formsResult = apiCall('/forms');
$forms = $formsResult['data']['forms'] ?? [];
$user = $_SESSION['user'] ?? [];
?>

<main>
    <div class="container" style="padding: 2rem 0;">
        <!-- Header -->
        <div class="dashboard-header">
            <div>
                <h1>My Forms</h1>
                <p style="color: #6b7280; margin-top: 0.5rem;">Create and manage your forms</p>
            </div>
            <a href="/form-builder.php" class="btn btn-primary">+ Create New Form</a>
        </div>

        <!-- Plan Info -->
        <?php if ($user): ?>
        <div class="card" style="background: #eff6ff; border: 1px solid #bfdbfe;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="color: #1e40af;"><?php echo strtoupper($user['subscription']['plan'] ?? 'FREE'); ?> Plan</strong>
                    <span style="margin-left: 1rem; color: #3b82f6;">
                        <?php echo count($forms); ?> forms created
                    </span>
                </div>
                <?php if (($user['subscription']['plan'] ?? 'free') === 'free'): ?>
                    <a href="/pricing.php" class="btn btn-primary">Upgrade Plan</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Forms List -->
        <?php if (empty($forms)): ?>
            <div class="card" style="text-align: center; padding: 4rem 2rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📝</div>
                <h2 style="margin-bottom: 0.5rem;">No forms yet</h2>
                <p style="color: #6b7280; margin-bottom: 2rem;">Create your first form to get started</p>
                <a href="/form-builder.php" class="btn btn-primary">Create Your First Form</a>
            </div>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($forms as $form): ?>
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div style="flex: 1;">
                                <h3 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($form['title']); ?></h3>
                                <p style="font-size: 0.875rem; color: #6b7280; line-height: 1.4;">
                                    <?php echo htmlspecialchars($form['description'] ?: 'No description'); ?>
                                </p>
                            </div>
                            <span class="badge badge-<?php echo $form['status']; ?>" style="
                                padding: 0.25rem 0.75rem;
                                border-radius: 9999px;
                                font-size: 0.75rem;
                                font-weight: 500;
                                background: <?php echo $form['status'] === 'published' ? '#d1fae5' : '#f3f4f6'; ?>;
                                color: <?php echo $form['status'] === 'published' ? '#065f46' : '#374151'; ?>;
                            "><?php echo $form['status']; ?></span>
                        </div>

                        <div style="display: flex; gap: 2rem; font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
                            <span>👁️ <?php echo $form['views']; ?> views</span>
                            <span>📬 <?php echo $form['submissions']; ?> responses</span>
                        </div>

                        <div style="display: flex; gap: 0.5rem;">
                            <a href="/form-builder.php?id=<?php echo $form['id']; ?>" class="btn btn-outline" style="flex: 1; text-align: center;">Edit</a>
                            <a href="/responses.php?id=<?php echo $form['id']; ?>" class="btn btn-secondary" style="flex: 1; text-align: center;">Responses</a>
                            <button onclick="deleteForm('<?php echo $form['id']; ?>')" class="btn btn-secondary">🗑️</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
function deleteForm(formId) {
    if (!confirm('Are you sure you want to delete this form?')) {
        return;
    }

    $.ajax({
        url: '/backend/public/api/forms/' + formId,
        method: 'DELETE',
        headers: {
            'Authorization': 'Bearer <?php echo $_SESSION['token'] ?? ''; ?>'
        },
        success: function() {
            location.reload();
        },
        error: function() {
            alert('Failed to delete form');
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
