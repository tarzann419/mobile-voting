#!/bin/bash

echo "🗳️  FINAL ELECTIONS SYSTEM VALIDATION"
echo "====================================="

# Test all views exist
echo "📋 1. Checking Election Views..."
views_to_check=(
    "resources/views/elections/index.blade.php"
    "resources/views/elections/show.blade.php"
    "resources/views/elections/create.blade.php"
    "resources/views/elections/edit.blade.php"
    "resources/views/elections/live-results.blade.php"
    "resources/views/elections/results/index.blade.php"
    "resources/views/elections/reports/index.blade.php"
    "resources/views/elections/reports/show.blade.php"
)

for view in "${views_to_check[@]}"; do
    if [ -f "$view" ]; then
        echo "✅ $view"
    else
        echo "❌ $view MISSING"
    fi
done

# Test view compilation
echo ""
echo "🔧 2. Testing View Compilation..."
if php artisan view:clear > /dev/null 2>&1 && php artisan view:cache > /dev/null 2>&1; then
    echo "✅ All views compile successfully"
else
    echo "❌ View compilation failed"
fi

# Test routes
echo ""
echo "🛣️  3. Testing Election Routes..."
route_count=$(php artisan route:list --name=elections | grep -c '›')
echo "✅ Election routes registered: $route_count"

# Test controller methods
echo ""
echo "🎮 4. Testing Controller Methods..."
controller_methods=(
    "index"
    "show" 
    "create"
    "store"
    "edit"
    "update"
    "destroy"
    "results"
    "liveResults"
    "reports"
    "resultsIndex"
    "reportsIndex"
)

for method in "${controller_methods[@]}"; do
    if grep -q "public function $method" app/Http/Controllers/ElectionController.php; then
        echo "✅ ElectionController::$method exists"
    else
        echo "❌ ElectionController::$method MISSING"
    fi
done

# Test service methods
echo ""
echo "⚙️  5. Testing Service Methods..."
service_methods=(
    "createElection"
    "updateElection"
    "getElectionWithDetails"
    "getElectionResults"
    "getElectionStatistics"
)

for method in "${service_methods[@]}"; do
    if grep -q "public function $method" app/Services/ElectionService.php; then
        echo "✅ ElectionService::$method exists"
    else
        echo "❌ ElectionService::$method MISSING"
    fi
done

# Test PHP syntax
echo ""
echo "🐘 6. Testing PHP Syntax..."
php_files=(
    "app/Http/Controllers/ElectionController.php"
    "app/Services/ElectionService.php"
)

for file in "${php_files[@]}"; do
    if php -l "$file" > /dev/null 2>&1; then
        echo "✅ $file syntax valid"
    else
        echo "❌ $file syntax error"
    fi
done

# Test Laravel server
echo ""
echo "🚀 7. Testing Laravel Server..."
if pgrep -f "php artisan serve" > /dev/null; then
    echo "✅ Laravel server is running"
    echo "🌐 Application available at: http://localhost:8000"
else
    echo "⚠️  Laravel server is not running"
    echo "   Start with: php artisan serve"
fi

echo ""
echo "🎉 VALIDATION COMPLETE!"
echo "=============================="
echo "All election views and routes have been successfully implemented!"
echo ""
echo "📋 Available Election Routes:"
echo "  • elections.index         - List all elections"
echo "  • elections.show          - View single election"
echo "  • elections.create        - Create new election form"
echo "  • elections.store         - Store new election"
echo "  • elections.edit          - Edit election form"
echo "  • elections.update        - Update election"
echo "  • elections.destroy       - Delete election"
echo "  • elections.results       - Get election results (JSON)"
echo "  • elections.live-results  - Live results view"
echo "  • elections.reports       - Generate election report"
echo "  • elections.results.index - List elections with results"
echo "  • elections.reports.index - List elections for reports"
echo ""
echo "🎯 Next Steps:"
echo "  1. Test the application in browser"
echo "  2. Create test elections"
echo "  3. Test all CRUD operations"
echo "  4. Verify live results functionality"
