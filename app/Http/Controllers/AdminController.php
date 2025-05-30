<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Models\Election;
use App\Models\Vote;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function organizations()
    {
        $organizations = Organization::with(['users', 'elections'])
            ->withCount(['users', 'elections'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.organizations.index', compact('organizations'));
    }

    public function users(Request $request)
    {
        $query = User::with('organization');

        // Handle search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('organization', function ($orgQuery) use ($search) {
                        $orgQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Handle role filter
        if ($request->filled('role') && $request->get('role') !== 'all') {
            $query->where('role', $request->get('role'));
        }

        // Handle status filter
        if ($request->filled('status') && $request->get('status') !== 'all') {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        // Handle organization filter
        if ($request->filled('organization') && $request->get('organization') !== 'all') {
            $query->where('organization_id', $request->get('organization'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Preserve query parameters in pagination links
        $users->appends($request->query());

        // Get organizations for filter dropdown
        $organizations = \App\Models\Organization::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'organizations'));
    }

    public function systemReports()
    {
        $stats = [
            'total_organizations' => Organization::count(),
            'total_users' => User::count(),
            'total_elections' => Election::count(),
            'total_votes' => Vote::count(),
            'active_elections' => Election::where('status', 'active')->count(),
            'completed_elections' => Election::where('status', 'completed')->count(),
        ];

        // Monthly registration trends
        $monthlyOrgs = Organization::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyUsers = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.reports.system', compact('stats', 'monthlyOrgs', 'monthlyUsers'));
    }
}
