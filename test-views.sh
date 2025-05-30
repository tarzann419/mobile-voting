#!/bin/bash

echo "=== Testing View Files ==="

# Test view compilation
echo "1. Testing view compilation..."
php artisan view:clear
php artisan view:cache

if [ $? -eq 0 ]; then
    echo "✅ All views compile successfully"
else
    echo "❌ View compilation failed"
    exit 1
fi

# Test key routes exist
echo ""
echo "2. Testing key routes..."

# Check elections routes
php artisan route:list --name=elections.index --format=compact | grep -q "elections.index"
if [ $? -eq 0 ]; then
    echo "✅ elections.index route exists"
else
    echo "❌ elections.index route missing"
fi

# Check positions routes
php artisan route:list --name=positions.index --format=compact | grep -q "positions.index"
if [ $? -eq 0 ]; then
    echo "✅ positions.index route exists"
else
    echo "❌ positions.index route missing"
fi

# Check admin routes
php artisan route:list --name=admin.organizations.index --format=compact | grep -q "admin.organizations.index"
if [ $? -eq 0 ]; then
    echo "✅ admin.organizations.index route exists"
else
    echo "❌ admin.organizations.index route missing"
fi

echo ""
echo "3. Testing view files exist..."

# Check critical view files
views=(
    "resources/views/elections/index.blade.php"
    "resources/views/positions/index.blade.php"
    "resources/views/positions/edit.blade.php"
    "resources/views/admin/organizations/index.blade.php"
    "resources/views/admin/users/index.blade.php"
    "resources/views/admin/reports/system.blade.php"
    "resources/views/elections/results/index.blade.php"
)

for view in "${views[@]}"; do
    if [ -f "$view" ]; then
        echo "✅ $view exists"
    else
        echo "❌ $view missing"
    fi
done

echo ""
echo "=== View Testing Complete ==="
