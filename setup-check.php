<?php
/**
 * FormFlow Setup Verification Script
 * Visit this page after deploying to verify your setup
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$checks = [];
$allPassed = true;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FormFlow Setup Check</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f3f4f6;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 2rem; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .content { padding: 30px; }
        .check-item {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 15px;
            background: #fafafa;
        }
        .check-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .check-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }
        .badge {
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-error { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .check-details {
            color: #6b7280;
            font-size: 0.95rem;
            margin-top: 10px;
        }
        .check-details code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        .fix-steps {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .fix-steps strong { color: #92400e; display: block; margin-bottom: 8px; }
        .fix-steps ol { margin-left: 20px; }
        .fix-steps li { margin: 5px 0; color: #78350f; }
        .summary {
            background: #f3f4f6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .summary-pass { background: #d1fae5; }
        .summary-fail { background: #fee2e2; }
        .summary h2 { margin-bottom: 10px; }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 10px;
        }
        .btn:hover { background: #5568d3; }
        pre {
            background: #1f2937;
            color: #f3f4f6;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 0.9rem;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 FormFlow Setup Check</h1>
            <p>Verifying your installation and configuration</p>
        </div>

        <div class="content">
            <?php

            // Check 1: PHP Version
            echo '<div class="check-item">';
            $phpVersion = phpversion();
            $phpOk = version_compare($phpVersion, '8.0', '>=');
            if (!$phpOk) $allPassed = false;

            echo '<div class="check-header">';
            echo '<span class="check-title">1. PHP Version</span>';
            echo '<span class="badge ' . ($phpOk ? 'badge-success' : 'badge-error') . '">';
            echo $phpOk ? '✓ PASS' : '✗ FAIL';
            echo '</span></div>';
            echo '<div class="check-details">Your PHP version: <code>' . $phpVersion . '</code></div>';
            if (!$phpOk) {
                echo '<div class="fix-steps"><strong>Fix:</strong> Upgrade to PHP 8.0 or higher</div>';
            }
            echo '</div>';

            // Check 2: Required PHP Extensions
            echo '<div class="check-item">';
            $requiredExtensions = ['curl', 'json', 'mbstring', 'mongodb'];
            $missingExtensions = [];
            foreach ($requiredExtensions as $ext) {
                if (!extension_loaded($ext)) {
                    $missingExtensions[] = $ext;
                }
            }
            $extensionsOk = empty($missingExtensions);
            if (!$extensionsOk) $allPassed = false;

            echo '<div class="check-header">';
            echo '<span class="check-title">2. PHP Extensions</span>';
            echo '<span class="badge ' . ($extensionsOk ? 'badge-success' : 'badge-error') . '">';
            echo $extensionsOk ? '✓ PASS' : '✗ FAIL';
            echo '</span></div>';

            if ($extensionsOk) {
                echo '<div class="check-details">All required extensions are loaded: ' . implode(', ', $requiredExtensions) . '</div>';
            } else {
                echo '<div class="check-details">Missing extensions: <code>' . implode(', ', $missingExtensions) . '</code></div>';
                echo '<div class="fix-steps"><strong>Fix:</strong>';
                echo '<ol>';
                foreach ($missingExtensions as $ext) {
                    if ($ext === 'mongodb') {
                        echo '<li>Install MongoDB extension: <code>sudo pecl install mongodb</code></li>';
                        echo '<li>Add to php.ini: <code>extension=mongodb.so</code></li>';
                    } else {
                        echo '<li>Install ' . $ext . ': <code>sudo apt install php-' . $ext . '</code></li>';
                    }
                }
                echo '<li>Restart Apache: <code>sudo systemctl restart apache2</code></li>';
                echo '</ol></div>';
            }
            echo '</div>';

            // Check 3: Backend API Accessibility
            echo '<div class="check-item">';
            $apiUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/backend/api/health';
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $apiOk = ($httpCode === 200);
            if (!$apiOk) $allPassed = false;

            echo '<div class="check-header">';
            echo '<span class="check-title">3. Backend API</span>';
            echo '<span class="badge ' . ($apiOk ? 'badge-success' : 'badge-error') . '">';
            echo $apiOk ? '✓ PASS' : '✗ FAIL';
            echo '</span></div>';
            echo '<div class="check-details">Testing: <code>' . $apiUrl . '</code></div>';
            echo '<div class="check-details">HTTP Status: <code>' . ($httpCode ?: 'Connection Failed') . '</code></div>';

            if ($apiOk) {
                echo '<div class="check-details">Response: <code>' . htmlspecialchars($response) . '</code></div>';
            } else {
                echo '<div class="fix-steps"><strong>Fix:</strong>';
                echo '<ol>';
                echo '<li>Check .htaccess exists in root folder</li>';
                echo '<li>Ensure Apache mod_rewrite is enabled: <code>sudo a2enmod rewrite</code></li>';
                echo '<li>Restart Apache: <code>sudo systemctl restart apache2</code></li>';
                echo '<li>Check backend/public/index.php exists</li>';
                if ($curlError) {
                    echo '<li>cURL Error: <code>' . htmlspecialchars($curlError) . '</code></li>';
                }
                echo '</ol></div>';
            }
            echo '</div>';

            // Check 4: Backend Configuration File
            echo '<div class="check-item">';
            $configExists = file_exists(__DIR__ . '/backend/config/config.php');
            if (!$configExists) $allPassed = false;

            echo '<div class="check-header">';
            echo '<span class="check-title">4. Backend Configuration</span>';
            echo '<span class="badge ' . ($configExists ? 'badge-success' : 'badge-error') . '">';
            echo $configExists ? '✓ PASS' : '✗ FAIL';
            echo '</span></div>';

            if ($configExists) {
                echo '<div class="check-details">Configuration file found: <code>backend/config/config.php</code></div>';
                echo '<div class="check-details" style="margin-top: 8px; color: #059669;">✓ No .env file needed - works out of the box!</div>';
            } else {
                echo '<div class="fix-steps"><strong>Fix:</strong>';
                echo '<p>Configuration file is missing. This should not happen with a fresh clone.</p>';
                echo '</div>';
            }
            echo '</div>';

            // Check 5: Composer Dependencies
            echo '<div class="check-item">';
            $vendorExists = file_exists(__DIR__ . '/backend/vendor/autoload.php');
            if (!$vendorExists) $allPassed = false;

            echo '<div class="check-header">';
            echo '<span class="check-title">5. Backend Dependencies</span>';
            echo '<span class="badge ' . ($vendorExists ? 'badge-success' : 'badge-error') . '">';
            echo $vendorExists ? '✓ PASS' : '✗ FAIL';
            echo '</span></div>';

            if ($vendorExists) {
                echo '<div class="check-details">Composer dependencies installed</div>';
            } else {
                echo '<div class="fix-steps"><strong>Fix:</strong>';
                echo '<ol>';
                echo '<li>Navigate to backend: <code>cd backend</code></li>';
                echo '<li>Install dependencies: <code>composer install</code></li>';
                echo '</ol></div>';
            }
            echo '</div>';

            // Check 6: MongoDB Connection
            echo '<div class="check-item">';
            $mongoOk = false;
            $mongoMessage = '';

            if (extension_loaded('mongodb') && $configExists) {
                try {
                    // Try to connect to MongoDB
                    $appConfig = require __DIR__ . '/backend/config/config.php';
                    $mongoUri = $appConfig['database']['uri'] ?? 'mongodb://localhost:27017';

                    $manager = new MongoDB\Driver\Manager($mongoUri);
                    $command = new MongoDB\Driver\Command(['ping' => 1]);
                    $manager->executeCommand('admin', $command);
                    $mongoOk = true;
                    $mongoMessage = 'Connected to MongoDB successfully';
                } catch (Exception $e) {
                    $mongoMessage = 'Cannot connect: ' . $e->getMessage();
                }
            } else {
                $mongoMessage = 'MongoDB extension not loaded or .env not found';
            }

            if (!$mongoOk) $allPassed = false;

            echo '<div class="check-header">';
            echo '<span class="check-title">6. MongoDB Connection</span>';
            echo '<span class="badge ' . ($mongoOk ? 'badge-success' : 'badge-error') . '">';
            echo $mongoOk ? '✓ PASS' : '✗ FAIL';
            echo '</span></div>';
            echo '<div class="check-details">' . htmlspecialchars($mongoMessage) . '</div>';

            if (!$mongoOk) {
                echo '<div class="fix-steps"><strong>Fix:</strong>';
                echo '<ol>';
                echo '<li>Install MongoDB: <code>sudo apt install mongodb-org</code></li>';
                echo '<li>Start MongoDB: <code>sudo systemctl start mongod</code></li>';
                echo '<li>Enable on boot: <code>sudo systemctl enable mongod</code></li>';
                echo '<li>Check status: <code>sudo systemctl status mongod</code></li>';
                echo '</ol></div>';
            }
            echo '</div>';

            // Check 7: Admin User Exists
            echo '<div class="check-item">';
            $adminExists = false;
            $adminMessage = '';

            if ($mongoOk) {
                try {
                    // Load config to get database name
                    $appConfig = require __DIR__ . '/backend/config/config.php';
                    $dbName = $appConfig['database']['database'] ?? 'formflow';

                    $query = new MongoDB\Driver\Query(['email' => 'admin@admin.com'], ['limit' => 1]);
                    $cursor = $manager->executeQuery($dbName . '.users', $query);
                    $users = $cursor->toArray();

                    if (count($users) > 0) {
                        $adminExists = true;
                        $adminMessage = 'Admin user found in database';
                    } else {
                        $adminMessage = 'Admin user not found in database';
                    }
                } catch (Exception $e) {
                    $adminMessage = 'Error checking admin user: ' . $e->getMessage();
                }
            } else {
                $adminMessage = 'Cannot check - MongoDB not connected';
            }

            if (!$adminExists) $allPassed = false;

            echo '<div class="check-header">';
            echo '<span class="check-title">7. Admin User</span>';
            echo '<span class="badge ' . ($adminExists ? 'badge-success' : 'badge-error') . '">';
            echo $adminExists ? '✓ PASS' : '✗ FAIL';
            echo '</span></div>';
            echo '<div class="check-details">' . htmlspecialchars($adminMessage) . '</div>';

            if (!$adminExists) {
                echo '<div class="fix-steps"><strong>Fix:</strong>';
                echo '<ol>';
                echo '<li>Run the admin creation script:</li>';
                echo '</ol>';
                echo '<pre>mongosh formflow < create-admin-user.js</pre>';
                echo '<p style="margin-top: 10px; color: #78350f;">This will create an admin user with:</p>';
                echo '<div style="margin: 10px 0; padding: 10px; background: white; border-radius: 4px;">';
                echo '<strong>Email:</strong> admin@admin.com<br>';
                echo '<strong>Password:</strong> admin';
                echo '</div></div>';
            }
            echo '</div>';

            // Check 8: Login Test
            echo '<div class="check-item">';
            $loginOk = false;
            $loginMessage = '';

            if ($apiOk && $adminExists) {
                $loginUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/backend/api/auth/login';
                $loginData = json_encode([
                    'email' => 'admin@admin.com',
                    'password' => 'admin'
                ]);

                $ch = curl_init($loginUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200) {
                    $responseData = json_decode($response, true);
                    if (isset($responseData['success']) && $responseData['success']) {
                        $loginOk = true;
                        $loginMessage = 'Login successful! Authentication is working correctly.';
                    } else {
                        $loginMessage = 'Login failed: ' . ($responseData['message'] ?? 'Unknown error');
                    }
                } else {
                    $loginMessage = 'Login request failed with HTTP ' . $httpCode;
                }
            } else {
                $loginMessage = 'Cannot test - previous checks must pass first';
            }

            if (!$loginOk && $apiOk && $adminExists) $allPassed = false;

            echo '<div class="check-header">';
            echo '<span class="check-title">8. Login Test</span>';
            echo '<span class="badge ' . ($loginOk ? 'badge-success' : ($apiOk && $adminExists ? 'badge-error' : 'badge-warning')) . '">';
            echo $loginOk ? '✓ PASS' : ($apiOk && $adminExists ? '✗ FAIL' : '⊘ SKIP');
            echo '</span></div>';
            echo '<div class="check-details">' . htmlspecialchars($loginMessage) . '</div>';

            if (!$loginOk && $apiOk && $adminExists) {
                echo '<div class="fix-steps"><strong>Troubleshoot:</strong>';
                echo '<ol>';
                echo '<li>Check backend logs for errors</li>';
                echo '<li>Verify JWT_SECRET is set in backend/.env</li>';
                echo '<li>Check MongoDB user password hash is correct</li>';
                echo '<li>Try deleting and recreating the admin user</li>';
                echo '</ol></div>';
            }
            echo '</div>';

            // Summary
            echo '<div class="summary ' . ($allPassed ? 'summary-pass' : 'summary-fail') . '">';
            if ($allPassed) {
                echo '<h2>✅ All Checks Passed!</h2>';
                echo '<p>Your FormFlow installation is ready to use.</p>';
                echo '<a href="login.php" class="btn">Go to Login Page</a>';
                echo '<div style="margin-top: 20px; color: #065f46;">';
                echo '<strong>Login Credentials:</strong><br>';
                echo 'Email: admin@admin.com<br>';
                echo 'Password: admin';
                echo '</div>';
            } else {
                echo '<h2>⚠️ Setup Incomplete</h2>';
                echo '<p>Please fix the failed checks above and refresh this page.</p>';
                echo '<a href="javascript:window.location.reload()" class="btn">Refresh Page</a>';
            }
            echo '</div>';

            ?>

            <div style="text-align: center; color: #6b7280; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <p>Need help? Check <code>SETUP.md</code> for detailed setup instructions.</p>
                <p style="margin-top: 10px;">Once everything is working, you can delete this file for security.</p>
            </div>
        </div>
    </div>
</body>
</html>
