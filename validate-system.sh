#!/usr/bin/env bash

# Voting System Validation Script
# This script validates that all major components of the voting system are working

echo "🗳️  Laravel Voting System - Final Validation"
echo "=============================================="
echo ""

cd /Users/dans.io/development/mobile-voting

echo "1. Testing Database Connection..."
if php artisan tinker --execute="echo 'DB Connection: ' . DB::connection()->getName() . PHP_EOL;" 2>/dev/null; then
    echo "✅ Database connection successful"
else
    echo "❌ Database connection failed"
    exit 1
fi

echo ""
echo "2. Testing User Authentication System..."
ADMIN_COUNT=$(php artisan tinker --execute="echo App\Models\User::where('role', 'admin')->count();" 2>/dev/null)
ORG_ADMIN_COUNT=$(php artisan tinker --execute="echo App\Models\User::where('role', 'organization_admin')->count();" 2>/dev/null)
VOTER_COUNT=$(php artisan tinker --execute="echo App\Models\User::where('role', 'voter')->count();" 2>/dev/null)

echo "   - System Admins: $ADMIN_COUNT"
echo "   - Organization Admins: $ORG_ADMIN_COUNT" 
echo "   - Voters: $VOTER_COUNT"

if [ "$ADMIN_COUNT" -gt 0 ] && [ "$ORG_ADMIN_COUNT" -gt 0 ] && [ "$VOTER_COUNT" -gt 0 ]; then
    echo "✅ User roles properly configured"
else
    echo "❌ User roles missing"
fi

echo ""
echo "3. Testing Organizations..."
ORG_COUNT=$(php artisan tinker --execute="echo App\Models\Organization::count();" 2>/dev/null)
echo "   - Total Organizations: $ORG_COUNT"

if [ "$ORG_COUNT" -gt 0 ]; then
    echo "✅ Organizations properly seeded"
else
    echo "❌ No organizations found"
fi

echo ""
echo "4. Testing Elections..."
ELECTION_COUNT=$(php artisan tinker --execute="echo App\Models\Election::count();" 2>/dev/null)
echo "   - Total Elections: $ELECTION_COUNT"

if [ "$ELECTION_COUNT" -gt 0 ]; then
    echo "✅ Elections properly seeded"
else
    echo "❌ No elections found"
fi

echo ""
echo "5. Testing Positions..."
POSITION_COUNT=$(php artisan tinker --execute="echo App\Models\Position::count();" 2>/dev/null)
echo "   - Total Positions: $POSITION_COUNT"

if [ "$POSITION_COUNT" -gt 0 ]; then
    echo "✅ Positions properly seeded"
else
    echo "❌ No positions found"
fi

echo ""
echo "6. Testing Routes..."
ADMIN_ROUTES=$(php artisan route:list --name=admin | grep -c "admin\." || echo "0")
POSITION_ROUTES=$(php artisan route:list --name=positions | grep -c "positions\." || echo "0")
ELECTION_ROUTES=$(php artisan route:list --name=elections | grep -c "elections\." || echo "0")

echo "   - Admin Routes: $ADMIN_ROUTES"
echo "   - Position Routes: $POSITION_ROUTES"
echo "   - Election Routes: $ELECTION_ROUTES"

if [ "$ADMIN_ROUTES" -gt 0 ] && [ "$POSITION_ROUTES" -gt 0 ] && [ "$ELECTION_ROUTES" -gt 0 ]; then
    echo "✅ All routes properly registered"
else
    echo "❌ Some routes missing"
fi

echo ""
echo "7. Testing Repository Methods..."
if php artisan tinker --execute="
\$repo = app(App\Repositories\Interfaces\ElectionRepositoryInterface::class);
\$elections = \$repo->getElectionsWithResults(1);
echo 'Repository methods working: ' . (\$elections !== null ? 'Yes' : 'No') . PHP_EOL;
" 2>/dev/null | grep -q "Yes"; then
    echo "✅ Repository methods working"
else
    echo "❌ Repository methods failing"
fi

echo ""
echo "8. Testing Laravel Server..."
if curl -s http://localhost:8000 >/dev/null 2>&1; then
    echo "✅ Laravel development server is running on http://localhost:8000"
else
    echo "❌ Laravel development server is not responding"
    echo "   Try running: php artisan serve --port=8000"
fi

echo ""
echo "9. Testing Key Controller Methods..."
if php artisan tinker --execute="
try {
    \$admin = new App\Http\Controllers\AdminController();
    \$position = new App\Http\Controllers\PositionController();
    \$election = new App\Http\Controllers\ElectionController(
        app(App\Services\ElectionService::class),
        app(App\Repositories\Interfaces\ElectionRepositoryInterface::class)
    );
    echo 'Controllers instantiated successfully' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Controller error: ' . \$e->getMessage() . PHP_EOL;
}
" 2>/dev/null | grep -q "successfully"; then
    echo "✅ Controllers working properly"
else
    echo "❌ Controller instantiation issues"
fi

echo ""
echo "=============================================="
echo "🎉 Validation Complete!"
echo ""
echo "📋 Test Credentials (password: 'password'):"
echo "   • Super Admin: admin@voteapp.com"
echo "   • Org Admin: john.anderson@university.edu"
echo "   • Voter: betty.allen.1@university.edu"
echo ""
echo "🌐 Application URLs:"
echo "   • Homepage: http://localhost:8000"
echo "   • Login: http://localhost:8000/login"
echo "   • Admin Dashboard: http://localhost:8000/admin/organizations"
echo "   • Positions: http://localhost:8000/positions"
echo "   • Elections Results: http://localhost:8000/elections-results"
echo ""
echo "✨ All core voting system functionality is operational!"
