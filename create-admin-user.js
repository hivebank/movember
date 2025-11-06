// MongoDB script to create admin user
// Run: mongosh formflow < create-admin-user.js

use formflow;

// Check if admin exists
const existingAdmin = db.users.findOne({ email: 'admin@admin.com' });

if (existingAdmin) {
    print('Admin user already exists!');
    print('Email: admin@admin.com');
    print('Password: admin');
} else {
    // Create admin user
    // Password 'admin' hashed with bcrypt
    const admin = {
        email: 'admin@admin.com',
        password: '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // bcrypt hash of 'admin'
        name: 'Admin User',
        company: 'FormFlow',
        subscription: {
            plan: 'enterprise',
            status: 'active',
            stripeCustomerId: null,
            stripeSubscriptionId: null,
            currentPeriodEnd: null
        },
        createdAt: new Date(),
        updatedAt: new Date()
    };

    db.users.insertOne(admin);

    print('✅ Admin user created successfully!');
    print('');
    print('Login credentials:');
    print('Email: admin@admin.com');
    print('Password: admin');
    print('');
    print('You can now login at your site!');
}
