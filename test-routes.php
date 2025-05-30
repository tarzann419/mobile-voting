<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test route accessibility
$routes = [
    '/candidates',
    '/candidate/register',
    '/admin/candidates/create',
    '/candidates/create'
];

echo "Testing route accessibility:\n";
echo "===========================\n\n";

foreach ($routes as $route) {
    try {
        $request = Illuminate\Http\Request::create($route, 'GET');
        $response = $kernel->handle($request);

        echo "Route: $route\n";
        echo "Status: " . $response->getStatusCode() . "\n";

        if ($response->getStatusCode() === 302) {
            $location = $response->headers->get('Location');
            echo "Redirect to: $location\n";
        }

        echo "---\n";
    } catch (Exception $e) {
        echo "Route: $route\n";
        echo "Error: " . $e->getMessage() . "\n";
        echo "---\n";
    }
}
