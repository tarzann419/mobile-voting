<?php

namespace App\Http\Controllers;

use App\Services\ElectionService;
use App\Repositories\Interfaces\ElectionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ElectionController extends Controller
{
    public function __construct(
        private ElectionService $electionService,
        private ElectionRepositoryInterface $electionRepository
    ) {}

    public function index(Request $request)
    {
        $organizationId = Auth::user()->organization_id;
        $elections = $this->electionRepository->paginate($organizationId);

        return view('elections.index', compact('elections'));
    }

    public function show($id)
    {
        $election = $this->electionService->getElectionWithDetails((int) $id);

        if (!$election) {
            abort(404, 'Election not found');
        }

        // Check if user has access to this election
        if (Auth::user()->organization_id !== $election->organization_id) {
            abort(403, 'Unauthorized access');
        }

        return view('elections.show', compact('election'));
    }

    public function create()
    {
        return view('elections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'registration_start_date' => 'required|date|after:now',
            'registration_end_date' => 'required|date|after:registration_start_date',
            'voting_start_date' => 'required|date|after:registration_end_date',
            'voting_end_date' => 'required|date|after:voting_start_date',
            'allow_multiple_votes' => 'boolean',
            'require_payment' => 'boolean',
        ]);

        $validated['organization_id'] = Auth::user()->organization_id;

        try {
            $election = $this->electionService->createElection($validated);
            return redirect()->route('elections.show', $election->id)
                ->with('success', 'Election created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function results($id): JsonResponse
    {
        try {
            $results = $this->electionService->getElectionResults((int) $id);
            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function resultsIndex()
    {
        $organizationId = Auth::user()->organization_id;
        $elections = $this->electionRepository->getElectionsWithResults($organizationId);

        return view('elections.results.index', compact('elections'));
    }

    public function reportsIndex()
    {
        $organizationId = Auth::user()->organization_id;
        $elections = $this->electionRepository->getElectionsForReports($organizationId);

        return view('elections.reports.index', compact('elections'));
    }

    public function edit($id)
    {
        $election = $this->electionService->getElectionWithDetails((int) $id);

        if (!$election) {
            abort(404, 'Election not found');
        }

        // Check if user has access to this election
        if (Auth::user()->organization_id !== $election->organization_id) {
            abort(403, 'Unauthorized access');
        }

        // Only allow editing if election is in draft status
        if ($election->status !== 'draft') {
            return redirect()->route('elections.show', $election->id)
                ->with('error', 'Only draft elections can be edited.');
        }

        return view('elections.edit', compact('election'));
    }

    public function update(Request $request, int $id)
    {
        $election = $this->electionService->getElectionWithDetails($id);

        if (!$election) {
            abort(404, 'Election not found');
        }

        // Check if user has access to this election
        if (Auth::user()->organization_id !== $election->organization_id) {
            abort(403, 'Unauthorized access');
        }

        // Only allow updating if election is in draft status
        if ($election->status !== 'draft') {
            return redirect()->route('elections.show', $election->id)
                ->with('error', 'Only draft elections can be updated.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'registration_start_date' => 'required|date|after:now',
            'registration_end_date' => 'required|date|after:registration_start_date',
            'voting_start_date' => 'required|date|after:registration_end_date',
            'voting_end_date' => 'required|date|after:voting_start_date',
            'allow_multiple_votes' => 'boolean',
            'require_payment' => 'boolean',
        ]);

        try {
            // Debug logging
            Log::info('Election update attempt', [
                'election_id' => $id,
                'validated_data' => $validated,
                'request_data' => $request->all()
            ]);

            $updatedElection = $this->electionService->updateElection($id, $validated);

            Log::info('Election updated successfully', [
                'election_id' => $updatedElection->id,
                'updated_data' => $updatedElection->toArray()
            ]);

            return redirect()->route('elections.show', $updatedElection->id)
                ->with('success', 'Election updated successfully.');
        } catch (\Exception $e) {
            Log::error('Election update failed', [
                'election_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $election = $this->electionService->getElectionWithDetails((int) $id);

        if (!$election) {
            abort(404, 'Election not found');
        }

        // Check if user has access to this election
        if (Auth::user()->organization_id !== $election->organization_id) {
            abort(403, 'Unauthorized access');
        }

        // Only allow deletion if election is in draft status
        if ($election->status !== 'draft') {
            return redirect()->route('elections.show', $election->id)
                ->with('error', 'Only draft elections can be deleted.');
        }

        try {
            $this->electionService->deleteElection($id);
            return redirect()->route('elections.index')
                ->with('success', 'Election deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('elections.show', $election->id)
                ->with('error', $e->getMessage());
        }
    }

    public function liveResults($id)
    {
        $election = $this->electionService->getElectionWithDetails((int) $id);

        if (!$election) {
            abort(404, 'Election not found');
        }

        // Check if user has access to this election
        if (Auth::user()->organization_id !== $election->organization_id) {
            abort(403, 'Unauthorized access');
        }

        // Only show live results for active elections
        if ($election->status !== 'active') {
            return redirect()->route('elections.show', $election->id)
                ->with('error', 'Live results are only available for active elections.');
        }

        try {
            $results = $this->electionService->getElectionResults($id);
            return view('elections.live-results', compact('election', 'results'));
        } catch (\Exception $e) {
            return redirect()->route('elections.show', $election->id)
                ->with('error', $e->getMessage());
        }
    }

    public function reports($id)
    {
        $election = $this->electionService->getElectionWithDetails((int) $id);

        if (!$election) {
            abort(404, 'Election not found');
        }

        // Check if user has access to this election
        if (Auth::user()->organization_id !== $election->organization_id) {
            abort(403, 'Unauthorized access');
        }

        // Only generate reports for completed elections
        if ($election->status !== 'completed') {
            return redirect()->route('elections.show', $election->id)
                ->with('error', 'Reports are only available for completed elections.');
        }

        try {
            $results = $this->electionService->getElectionResults($id);
            $statistics = $this->electionService->getElectionStatistics($id);
            return view('elections.reports.show', compact('election', 'results', 'statistics'));
        } catch (\Exception $e) {
            return redirect()->route('elections.show', $election->id)
                ->with('error', $e->getMessage());
        }
    }
}
