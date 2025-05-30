<?php

namespace App\Livewire;

use App\Services\VotingService;
use Livewire\Component;

class VotingBallot extends Component
{
    public $electionId;
    public $availablePositions = [];
    public $votingHistory = [];
    public $selectedCandidates = [];
    public $isVoting = false;

    public function __construct(
        private VotingService $votingService
    ) {}

    public function mount($electionId)
    {
        $this->electionId = $electionId;
        $this->loadVotingData();
    }

    public function loadVotingData()
    {
        $userId = auth()->id();

        // Check if user can vote
        $eligibility = $this->votingService->canUserVote($userId, $this->electionId);

        if (!$eligibility['can_vote']) {
            session()->flash('error', $eligibility['reason']);
            return;
        }

        $this->availablePositions = $this->votingService->getAvailablePositionsToVote($userId, $this->electionId);
        $this->votingHistory = $this->votingService->getUserVotingHistory($userId, $this->electionId);
    }

    public function selectCandidate($positionId, $candidateId)
    {
        $this->selectedCandidates[$positionId] = $candidateId;
    }

    public function castVote($positionId)
    {
        if (!isset($this->selectedCandidates[$positionId])) {
            session()->flash('error', 'Please select a candidate first.');
            return;
        }

        $this->isVoting = true;

        try {
            $voteData = [
                'election_id' => $this->electionId,
                'position_id' => $positionId,
                'candidate_id' => $this->selectedCandidates[$positionId],
                'user_id' => auth()->id(),
            ];

            $vote = $this->votingService->castVote($voteData);

            // Remove the position from available positions
            $this->availablePositions = array_filter($this->availablePositions, function ($position) use ($positionId) {
                return $position['position']['id'] != $positionId;
            });

            // Add to voting history
            $this->votingHistory[] = [
                'position' => collect($this->availablePositions)->firstWhere('position.id', $positionId)['position']['title'] ?? 'Unknown',
                'candidate' => 'Vote Cast',
                'voted_at' => now(),
            ];

            // Clear selection
            unset($this->selectedCandidates[$positionId]);

            session()->flash('success', 'Vote cast successfully! Your vote hash: ' . $vote->vote_hash);

            // Emit event for real-time updates
            $this->dispatch('vote-cast');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        } finally {
            $this->isVoting = false;
        }
    }

    public function render()
    {
        return view('livewire.voting-ballot');
    }
}
