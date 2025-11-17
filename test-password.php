<?php
/**
 * Password Testing and Debug Script
 * Place this in: C:\xampp\htdocs\Kyle-HMS\test-password.php
 * Access via: http://localhost/Kyle-HMS/test-password.php
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Password Debug Test</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .code { background: #f8f9fa; padding: 10px; border-left: 3px solid #007bff; font-family: monospace; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f8f9fa; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h2>🔍 Kyle-HMS Password Debug Tool</h2>";

// Test 1: Database Connection
echo "<h3>1. Database Connection Test</h3>";
try {
    if (isset($conn)) {
        echo "<div class='success'>✅ Database connection successful!</div>";
    } else {
        echo "<div class='error'>❌ Database connection failed - \$conn variable not found</div>";
        exit;
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Database error: " . $e->getMessage() . "</div>";
    exit;
}

// Test 2: Generate correct password hash
echo "<h3>2. Generate Password Hash for 'Test@123'</h3>";
$testPassword = 'Test@123';
$newHash = password_hash($testPassword, PASSWORD_BCRYPT, ['cost' => 12]);
echo "<div class='info'>";
echo "<strong>Password:</strong> Test@123<br>";
echo "<strong>New Hash:</strong><br>";
echo "<div class='code'>" . $newHash . "</div>";
echo "</div>";

// Test 3: Check existing users
echo "<h3>3. Current Users in Database</h3>";
try {
    $stmt = $conn->query("SELECT email, usertype, status, 
                          LEFT(password, 20) as password_preview,
                          created_at 
                          FROM webuser 
                          ORDER BY usertype, email");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        echo "<table>";
        echo "<tr><th>Email</th><th>Type</th><th>Status</th><th>Password Preview</th><th>Created</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['usertype']) . "</td>";
            echo "<td>" . htmlspecialchars($user['status']) . "</td>";
            echo "<td><code>" . htmlspecialchars($user['password_preview']) . "...</code></td>";
            echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='error'>❌ No users found in database. Please run the database setup SQL script first.</div>";
    }
} catch (PDOException $e) {
    echo "<div class='error'>❌ Error reading users: " . $e->getMessage() . "</div>";
}

// Test 4: Test password verification
echo "<h3>4. Password Verification Test</h3>";
$testEmails = ['admin@kyle-hms.com', 'patient1@test.com', 'dr.soklina@kyle-hms.com'];

foreach ($testEmails as $testEmail) {
    try {
        $stmt = $conn->prepare("SELECT email, password, status FROM webuser WHERE email = ?");
        $stmt->execute([$testEmail]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "<div class='info'>";
            echo "<strong>Testing:</strong> " . htmlspecialchars($testEmail) . "<br>";
            echo "<strong>Account Status:</strong> " . htmlspecialchars($user['status']) . "<br>";
            
            // Test with Test@123
            $passwordMatch = password_verify($testPassword, $user['password']);
            
            if ($passwordMatch) {
                echo "<strong>Result:</strong> <span style='color: green;'>✅ Password 'Test@123' WORKS!</span><br>";
            } else {
                echo "<strong>Result:</strong> <span style='color: red;'>❌ Password 'Test@123' does NOT work</span><br>";
                echo "<strong>Hash in DB:</strong><br><div class='code'>" . htmlspecialchars($user['password']) . "</div>";
            }
            echo "</div>";
        } else {
            echo "<div class='error'>❌ User not found: " . htmlspecialchars($testEmail) . "</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Error testing " . htmlspecialchars($testEmail) . ": " . $e->getMessage() . "</div>";
    }
}

// Test 5: Update password option
echo "<h3>5. Fix Passwords</h3>";
echo "<div class='info'>";
echo "<p>If passwords are not working, click the button below to update ALL user passwords to 'Test@123'</p>";
echo "<form method='post' style='margin: 0;'>";
echo "<button type='submit' name='fix_passwords' class='btn'>🔧 Update All Passwords to Test@123</button>";
echo "</form>";
echo "</div>";

if (isset($_POST['fix_passwords'])) {
    try {
        $conn->beginTransaction();
        
        // Generate fresh hash
        $correctHash = password_hash('Test@123', PASSWORD_BCRYPT, ['cost' => 12]);
        
        // Update all users
        $stmt = $conn->prepare("UPDATE webuser SET password = ?");
        $stmt->execute([$correctHash]);
        
        $rowCount = $stmt->rowCount();
        
        $conn->commit();
        
        echo "<div class='success'>";
        echo "✅ Successfully updated {$rowCount} user passwords!<br>";
        echo "All users can now login with password: <strong>Test@123</strong><br>";
        echo "<a href='test-password.php' class='btn'>🔄 Refresh Page</a>";
        echo "<a href='auth/login.php' class='btn'>🔐 Go to Login</a>";
        echo "</div>";
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "<div class='error'>❌ Error updating passwords: " . $e->getMessage() . "</div>";
    }
}

// Test 6: Login form test
echo "<h3>6. Direct Login Test</h3>";
echo "<div class='info'>";
echo "<p>Test login directly from here:</p>";
echo "<form method='post'>";
echo "<input type='email' name='test_email' placeholder='Email' style='padding: 10px; width: 300px; margin: 5px;' required><br>";
echo "<input type='password' name='test_password' placeholder='Password' style='padding: 10px; width: 300px; margin: 5px;' required><br>";
echo "<button type='submit' name='test_login' class='btn'>🔐 Test Login</button>";
echo "</form>";
echo "</div>";

if (isset($_POST['test_login'])) {
    $email = $_POST['test_email'] ?? '';
    $password = $_POST['test_password'] ?? '';
    
    try {
        $stmt = $conn->prepare("SELECT email, usertype, password, status FROM webuser WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            if ($user['status'] !== 'active') {
                echo "<div class='error'>❌ Account is {$user['status']}</div>";
            } elseif (password_verify($password, $user['password'])) {
                echo "<div class='success'>";
                echo "✅ Login Successful!<br>";
                echo "Email: {$user['email']}<br>";
                echo "Type: {$user['usertype']}<br>";
                echo "Status: {$user['status']}<br>";
                echo "<a href='auth/login.php' class='btn'>Go to Real Login Page</a>";
                echo "</div>";
            } else {
                echo "<div class='error'>";
                echo "❌ Invalid password<br>";
                echo "User exists but password doesn't match<br>";
                echo "Hash in database:<br>";
                echo "<div class='code'>" . htmlspecialchars($user['password']) . "</div>";
                echo "</div>";
            }
        } else {
            echo "<div class='error'>❌ User not found: {$email}</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
    }
}

echo "<hr>";
echo "<h3>📚 Quick Reference</h3>";
echo "<div class='info'>";
echo "<strong>Test Accounts:</strong><br>";
echo "• Admin: admin@kyle-hms.com<br>";
echo "• Doctor: dr.soklina@kyle-hms.com<br>";
echo "• Patient: patient1@test.com<br>";
echo "<strong>Password:</strong> Test@123<br><br>";
echo "<strong>Note:</strong> Delete this file (test-password.php) before going to production!";
echo "</div>";

echo "</div></body></html>";
?>