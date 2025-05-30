<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Organization;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoterAccreditation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Route to appropriate dashboard based on user role
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isOrganizationAdmin()) {
            return $this->organizationAdminDashboard();
        } else {
            return $this->voterDashboard();
        }
    }

    private function adminDashboard()
    {
        $stats = [
            'total_organizations' => Organization::count(),
            'active_elections' => Election::where('status', 'active')->count(),
            'total_users' => User::count(),
            'total_votes' => Vote::count(),
        ];

        // Get recent system activities (you can customize this based on your needs)
        $recent_activities = collect([
            [
                'description' => 'New organization registered: Tech Association',
                'created_at' => now()->subHours(2),
            ],
            [
                'description' => 'Election completed: Student Council 2024',
                'created_at' => now()->subHours(5),
            ],
            [
                'description' => 'New election created: Board of Directors',
                'created_at' => now()->subDay(),
            ],
        ]);

        return view('dashboards.admin', compact('stats', 'recent_activities'));
    }

    private function organizationAdminDashboard()
    {
        $user = Auth::user();
        $organization = $user->organization;

        $stats = [
            'total_elections' => $organization->elections()->count(),
            'active_elections' => $organization->elections()->where('status', 'active')->count(),
            'total_voters' => $organization->users()->where('role', 'voter')->count(),
            'total_candidates' => $organization->elections()
                ->with('positions.candidates')
                ->get()
                ->flatMap(function ($election) {
                    return $election->positions->flatMap->candidates;
                })->count(),
        ];

        $current_elections = $organization->elections()
            ->whereIn('status', ['active', 'scheduled'])
            ->with('positions')
            ->orderBy('voting_start_date', 'desc')
            ->take(5)
            ->get();

        return view('dashboards.organization-admin', compact('stats', 'current_elections'));
    }

    private function voterDashboard()
    {
        $user = Auth::user();
        $organization = $user->organization;

        // If user doesn't have an organization, show minimal dashboard
        if (!$organization) {
            $voter_status = [
                'is_accredited' => false,
                'elections_participated' => 0,
                'total_votes_cast' => 0,
            ];

            $available_elections = collect();
            $voting_history = collect();

            return view('dashboards.voter', compact('voter_status', 'available_elections', 'voting_history'));
        }

        // Check voter accreditation status
        $accreditation = VoterAccreditation::where('user_id', $user->id)
            ->where('organization_id', $organization->id)
            ->first();

        $voter_status = [
            'is_accredited' => $accreditation && $accreditation->status === 'approved',
            'elections_participated' => Vote::where('user_id', $user->id)->distinct('election_id')->count(),
            'total_votes_cast' => Vote::where('user_id', $user->id)->count(),
        ];

        // Get available elections
        $available_elections = $organization->elections()
            ->whereIn('status', ['active', 'scheduled', 'completed'])
            ->with(['positions', 'votes' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('voting_start_date', 'desc')
            ->get();

        // Get voting history
        $voting_history = Vote::where('user_id', $user->id)
            ->with('election')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->groupBy('election_id')
            ->map(function ($votes) {
                return $votes->first(); // Get one vote per election for history
            })
            ->values();

        return view('dashboards.voter', compact('voter_status', 'available_elections', 'voting_history'));
    }
}
