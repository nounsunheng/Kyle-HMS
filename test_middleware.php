<?php
require_once 'vendor/autoload.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CsrfMiddleware;

echo "🧪 Testing Kyle-HMS Middleware\n";
echo "================================\n\n";

// Test 1: CSRF Token Generation
echo "1️⃣ Testing CSRF Protection...\n";
$csrfToken = CsrfMiddleware::token();
echo "   ✅ Token generated: " . substr($csrfToken, 0, 20) . "...\n";

$csrfField = CsrfMiddleware::field();
echo "   ✅ Field HTML generated: " . (strlen($csrfField) > 0 ? 'YES' : 'NO') . "\n";

// Verify token
$isValid = CsrfMiddleware::verify($csrfToken);
echo "   ✅ Token verification: " . ($isValid ? 'PASS' : 'FAIL') . "\n";
echo "\n";

// Test 2: Auth Check (static method)
echo "2️⃣ Testing Authentication Check...\n";
$isAuthenticated = AuthMiddleware::check();
echo "   " . ($isAuthenticated ? '✅' : '❌') . " User authenticated: " . ($isAuthenticated ? 'YES' : 'NO') . "\n";
echo "\n";

// Test 3: Role Helpers
echo "3️⃣ Testing Role Helpers...\n";
if (isLoggedIn()) {
    echo "   ✅ User role: " . currentUserRole() . "\n";
    echo "   ✅ Is Patient: " . (RoleMiddleware::isPatient() ? 'YES' : 'NO') . "\n";
    echo "   ✅ Is Doctor: " . (RoleMiddleware::isDoctor() ? 'YES' : 'NO') . "\n";
    echo "   ✅ Is Admin: " . (RoleMiddleware::isAdmin() ? 'YES' : 'NO') . "\n";
} else {
    echo "   ℹ️  Not logged in - role checks skipped\n";
}
echo "\n";

echo "================================\n";
echo "✅ Middleware tests completed!\n";