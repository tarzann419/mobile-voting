<?php

namespace App\Http\Controllers;

use App\Services\CandidateService;
use App\Repositories\Interfaces\CandidateRepositoryInterface;
use App\Repositories\Interfaces\ElectionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidateController extends Controller
{
    public function __construct(
        private CandidateService $candidateService,
        private CandidateRepositoryInterface $candidateRepository,
        private ElectionRepositoryInterface $electionRepository
    ) {}

    public function index(Request $request)
    {
        $organizationId = Auth::user()->organization_id;

        // Prepare filters
        $filters = [];
        if ($request->filled('status')) {
            $filters['status'] = $request->status;
        }
        if ($request->filled('position_id')) {
            $filters['position_id'] = $request->position_id;
        }
        if ($request->filled('election_id')) {
            $filters['election_id'] = $request->election_id;
        }

        // Get paginated filtered candidates
        $candidates = $this->candidateRepository->findByOrganizationWithFiltersPaginated($organizationId, $filters, 10);

        // Get positions for filter dropdown
        $positions = collect();
        $elections = $this->electionRepository->findByOrganization($organizationId);
        foreach ($elections as $election) {
            $positions = $positions->merge($election->positions);
        }

        return view('candidates.index', compact('candidates', 'positions', 'elections'));
    }

    public function show($id)
    {
        $candidate = $this->candidateRepository->findById((int) $id);

        if (!$candidate) {
            abort(404, 'Candidate not found');
        }

        // Check if user has access to this candidate
        if (Auth::user()->organization_id !== $candidate->position->election->organization_id) {
            abort(403, 'Unauthorized access');
        }

        return view('candidates.show', compact('candidate'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        // Get available positions for current user's organization
        $elections = $this->electionRepository->findByOrganization($user->organization_id);
        $availablePositions = collect();

        foreach ($elections as $election) {
            // Only show positions from elections that allow candidate registration
            // This includes published elections (during registration period) and active elections (if still accepting candidates)
            if (in_array($election->status, ['published', 'active'])) {
                // Check if we're within the registration period
                $now = now();
                $registrationOpen = $now >= $election->registration_start_date && $now <= $election->registration_end_date;

                if ($registrationOpen) {
                    foreach ($election->positions as $position) {
                        // Check if user hasn't already registered for this position
                        $existingCandidate = $this->candidateRepository->findByUserAndPosition($user->id, $position->id);
                        if (!$existingCandidate) {
                            $availablePositions->push($position);
                        }
                    }
                }
            }
        }

        return view('candidates.create', compact('availablePositions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'position_id' => 'required|exists:positions,id',
            'bio' => 'required|string|max:1000',
            'manifesto' => 'required|string|max:5000',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['user_id'] = Auth::user()->id;

        try {
            $candidate = $this->candidateService->registerCandidate($validated);

            return redirect()->route('candidates.show', $candidate->id)
                ->with('success', 'Candidate registration submitted successfully! Your application is pending approval.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function approve(Request $request, int $id)
    {
        $candidate = $this->candidateRepository->findById($id);

        if (!$candidate) {
            abort(404, 'Candidate not found');
        }

        // Check if user has access to approve this candidate
        if (Auth::user()->organization_id !== $candidate->position->election->organization_id) {
            abort(403, 'Unauthorized access');
        }

        try {
            $this->candidateService->approveCandidate($id, Auth::user()->id);

            return back()->with('success', 'Candidate approved successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, int $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $candidate = $this->candidateRepository->findById($id);

        if (!$candidate) {
            abort(404, 'Candidate not found');
        }

        // Check if user has access to reject this candidate
        if (Auth::user()->organization_id !== $candidate->position->election->organization_id) {
            abort(403, 'Unauthorized access');
        }

        try {
            $this->candidateService->rejectCandidate($id, $validated['reason']);

            return back()->with('success', 'Candidate rejected successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin method to create candidates for users
     */
    public function adminCreate(Request $request)
    {
        $user = Auth::user();

        // Get available positions for current user's organization
        $elections = $this->electionRepository->findByOrganization($user->organization_id);
        $availablePositions = collect();

        foreach ($elections as $election) {
            // Only show positions from elections that allow candidate registration
            if (in_array($election->status, ['published', 'active'])) {
                // Check if we're within the registration period
                $now = now();
                $registrationOpen = $now >= $election->registration_start_date && $now <= $election->registration_end_date;

                if ($registrationOpen) {
                    $availablePositions = $availablePositions->merge($election->positions);
                }
            }
        }

        // Get organization users who can be candidates (voters in the organization who are not already candidates)
        $existingCandidateUserIds = $this->candidateRepository->findByOrganization($user->organization_id)
            ->pluck('user_id')
            ->unique();

        $organizationUsers = \App\Models\User::where('organization_id', $user->organization_id)
            ->where('role', 'voter')
            ->where('is_active', true)
            ->whereNotIn('id', $existingCandidateUserIds)
            ->orderBy('name')
            ->get();

        return view('candidates.admin-create', compact('availablePositions', 'organizationUsers'));
    }

    /**
     * Admin method to store candidates for users
     */
    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'position_id' => 'required|exists:positions,id',
            'bio' => 'required|string|max:1000',
            'manifesto' => 'required|string|max:5000',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'auto_approve' => 'boolean',
        ]);

        // Verify the user belongs to the same organization
        $targetUser = \App\Models\User::findOrFail($validated['user_id']);
        if ($targetUser->organization_id !== Auth::user()->organization_id) {
            abort(403, 'You can only create candidates for users in your organization');
        }

        // Verify the position belongs to the same organization
        $position = \App\Models\Position::with('election')->findOrFail($validated['position_id']);
        if ($position->election->organization_id !== Auth::user()->organization_id) {
            abort(403, 'You can only create candidates for positions in your organization');
        }

        try {
            $candidateData = [
                'user_id' => $validated['user_id'],
                'position_id' => $validated['position_id'],
                'bio' => $validated['bio'],
                'manifesto' => $validated['manifesto'],
                'profile_photo' => $validated['profile_photo'] ?? null,
                'status' => isset($validated['auto_approve']) && $validated['auto_approve'] ? 'approved' : 'pending',
                'approved_by' => isset($validated['auto_approve']) && $validated['auto_approve'] ? Auth::user()->id : null,
                'approved_at' => isset($validated['auto_approve']) && $validated['auto_approve'] ? now() : null,
            ];

            $candidate = $this->candidateService->registerCandidate($candidateData);

            $message = isset($validated['auto_approve']) && $validated['auto_approve']
                ? 'Candidate created and approved successfully!'
                : 'Candidate created successfully! The application is pending approval.';

            return redirect()->route('candidates.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
