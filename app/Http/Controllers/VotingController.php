<?php

namespace App\Http\Controllers;

use App\Services\VotingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class VotingController extends Controller
{
    public function __construct(
        private VotingService $votingService
    ) {}

    public function show($electionId)
    {
        $userId = Auth::id();

        // Check if user can vote
        $eligibility = $this->votingService->canUserVote($userId, (int) $electionId);

        if (!$eligibility['can_vote']) {
            return view('voting.ineligible', [
                'reason' => $eligibility['reason'],
                'electionId' => $electionId
            ]);
        }

        // Get available positions to vote for
        $availablePositions = $this->votingService->getAvailablePositionsToVote($userId, (int) $electionId);
        $votingHistory = $this->votingService->getUserVotingHistory($userId, (int) $electionId);

        return view('voting.ballot', compact('availablePositions', 'votingHistory', 'electionId'));
    }

    public function vote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'election_id' => 'required|exists:elections,id',
            'position_id' => 'required|exists:positions,id',
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        $userId = Auth::id();
        $candidateId = $validated['candidate_id'];
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        try {
            $vote = $this->votingService->castVote($userId, $candidateId, $ipAddress, $userAgent);

            return response()->json([
                'success' => true,
                'message' => 'Vote cast successfully',
                'vote_hash' => $vote->vote_hash,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function results($electionId): JsonResponse
    {
        try {
            $results = $this->votingService->getRealTimeResults((int) $electionId);
            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function stats($electionId): JsonResponse
    {
        try {
            // Simple stats implementation - can be enhanced later
            $stats = [
                'total_votes' => 0,
                'voter_turnout' => 0,
                'positions_count' => 0
            ];
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function dashboard($electionId)
    {
        return view('voting.dashboard', compact('electionId'));
    }

    public function ballot($election)
    {
        $userId = Auth::id();
        $electionId = $election;

        // Check if user can vote
        $eligibility = $this->votingService->canUserVote($userId, $electionId);

        if (!$eligibility['can_vote']) {
            return view('voting.ineligible', [
                'reason' => $eligibility['reason'],
                'election' => $election,
                'electionId' => $electionId
            ]);
        }

        // Get available positions to vote for
        $availablePositions = $this->votingService->getAvailablePositionsToVote($userId, $electionId);
        $votingHistory = $this->votingService->getUserVotingHistory($userId, $electionId);

        return view('voting.ballot', compact('availablePositions', 'votingHistory', 'election', 'electionId'));
    }

    public function castVote(Request $request, $election)
    {
        $validated = $request->validate([
            'position_id' => 'required|exists:positions,id',
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        $userId = Auth::id();
        $candidateId = $validated['candidate_id'];
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        try {
            $vote = $this->votingService->castVote($userId, $candidateId, $ipAddress, $userAgent);

            return response()->json([
                'success' => true,
                'message' => 'Vote cast successfully',
                'vote_hash' => $vote->vote_hash,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
