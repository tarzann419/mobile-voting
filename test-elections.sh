#!/bin/bash

echo "Testing Elections Views..."
echo "=========================="

# Test view compilation
echo "1. Testing view compilation..."
php artisan view:clear
if php artisan view:cache; then
    echo "✅ All views compile successfully"
else
    echo "❌ View compilation failed"
    exit 1
fi

# Check if all required election views exist
echo ""
echo "2. Checking required election views..."
views=(
    "resources/views/elections/index.blade.php"
    "resources/views/elections/show.blade.php" 
    "resources/views/elections/create.blade.php"
    "resources/views/elections/edit.blade.php"
    "resources/views/elections/results/index.blade.php"
    "resources/views/elections/reports/index.blade.php"
)

for view in "${views[@]}"; do
    if [ -f "$view" ]; then
        echo "✅ $view exists"
    else
        echo "❌ $view missing"
    fi
done

# Check routes
echo ""
echo "3. Testing election routes..."
if php artisan route:list --name=elections > /dev/null 2>&1; then
    echo "✅ All election routes registered"
    echo "Total election routes: $(php artisan route:list --name=elections | grep -c '›')"
else
    echo "❌ Route registration failed"
fi

# Test controller syntax
echo ""
echo "4. Testing controller syntax..."
if php -l app/Http/Controllers/ElectionController.php > /dev/null 2>&1; then
    echo "✅ ElectionController syntax valid"
else
    echo "❌ ElectionController syntax error"
fi

if php -l app/Services/ElectionService.php > /dev/null 2>&1; then
    echo "✅ ElectionService syntax valid"
else
    echo "❌ ElectionService syntax error"
fi

echo ""
echo "Testing completed!"
