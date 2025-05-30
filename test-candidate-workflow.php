<?php

// Simple test to verify candidate creation functionality
echo "Testing Candidate Creation Workflow\n";
echo "===================================\n\n";

// Test 1: Check if server is running
echo "1. Testing server connectivity...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8003');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 || $httpCode === 302) {
    echo "✅ Server is running and responding\n";
} else {
    echo "❌ Server connection failed (HTTP $httpCode)\n";
}
echo "\n";

// Test 2: Check candidate routes accessibility
echo "2. Testing candidate routes...\n";
$routes = [
    '/candidates' => 'Candidate index page',
    '/candidate/register' => 'Voter candidate registration',
    '/admin/candidates/create' => 'Admin candidate creation',
    '/candidates/create' => 'Create candidate form'
];

foreach ($routes as $route => $description) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8003$route");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);

    if ($httpCode === 302 && (strpos($redirectUrl, 'login') !== false)) {
        echo "✅ $description - Properly protected (redirects to login)\n";
    } elseif ($httpCode === 200) {
        echo "⚠️ $description - Accessible without authentication\n";
    } else {
        echo "❌ $description - Unexpected response (HTTP $httpCode)\n";
    }
}
echo "\n";

// Test 3: Check if critical files exist
echo "3. Testing file structure...\n";
$files = [
    'app/Http/Controllers/CandidateController.php' => 'CandidateController',
    'resources/views/candidates/index.blade.php' => 'Candidate index view',
    'resources/views/candidates/admin-create.blade.php' => 'Admin candidate creation view',
    'resources/views/candidates/create.blade.php' => 'Candidate creation view',
    'routes/web.php' => 'Web routes'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description exists\n";
    } else {
        echo "❌ $description is missing\n";
    }
}
echo "\n";

// Test 4: Check route definitions in routes file
echo "4. Testing route definitions...\n";
$routesContent = file_get_contents('routes/web.php');
$routeChecks = [
    'candidate/register' => 'Voter registration route',
    'admin/candidates/create' => 'Admin creation route',
    'candidates.store' => 'Candidate store route',
    'admin.candidates.store' => 'Admin store route'
];

foreach ($routeChecks as $route => $description) {
    if (strpos($routesContent, $route) !== false) {
        echo "✅ $description defined\n";
    } else {
        echo "❌ $description missing\n";
    }
}
echo "\n";

// Test 5: Check controller methods
echo "5. Testing controller methods...\n";
$controllerContent = file_get_contents('app/Http/Controllers/CandidateController.php');
$methods = [
    'public function index' => 'Index method',
    'public function create' => 'Create method (used for voter registration)',
    'public function store' => 'Store method',
    'public function adminCreate' => 'Admin create method',
    'public function adminStore' => 'Admin store method',
    'public function show' => 'Show method'
];

foreach ($methods as $method => $description) {
    if (strpos($controllerContent, $method) !== false) {
        echo "✅ $description exists\n";
    } else {
        echo "❌ $description missing\n";
    }
}
echo "\n";

echo "Test completed!\n";
echo "\nNext steps:\n";
echo "1. Login to the application at http://127.0.0.1:8003/login\n";
echo "2. Navigate to /candidates to see the candidate management interface\n";
echo "3. Test voter candidate registration via /candidate/register\n";
echo "4. Test admin candidate creation via /admin/candidates/create\n";
