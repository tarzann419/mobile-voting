<?php

namespace App\Livewire;

use App\Services\VotingService;
use Livewire\Component;
use Livewire\Attributes\On;

class RealTimeResults extends Component
{
    public $electionId;
    public $results = [];
    public $stats = [];
    public $autoRefresh = true;

    public function __construct(
        private VotingService $votingService
    ) {}

    public function mount($electionId)
    {
        $this->electionId = $electionId;
        $this->loadResults();
        $this->loadStats();
    }

    public function loadResults()
    {
        try {
            $this->results = $this->votingService->getRealTimeResults($this->electionId);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function loadStats()
    {
        try {
            $this->stats = $this->votingService->getVotingStats($this->electionId);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    #[On('vote-cast')]
    public function refreshResults()
    {
        $this->loadResults();
        $this->loadStats();
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    public function manualRefresh()
    {
        $this->loadResults();
        $this->loadStats();
        session()->flash('success', 'Results refreshed!');
    }

    public function getResultsByPosition()
    {
        $grouped = [];

        foreach ($this->results as $result) {
            $positionId = $result->position_id;

            if (!isset($grouped[$positionId])) {
                $grouped[$positionId] = [
                    'position_title' => $result->position_title,
                    'candidates' => [],
                    'total_votes' => 0
                ];
            }

            if ($result->candidate_id) {
                $grouped[$positionId]['candidates'][] = [
                    'candidate_id' => $result->candidate_id,
                    'candidate_name' => $result->candidate_name,
                    'vote_count' => $result->vote_count,
                    'percentage' => $result->percentage ?? 0
                ];

                $grouped[$positionId]['total_votes'] += $result->vote_count;
            }
        }

        // Sort candidates by vote count
        foreach ($grouped as &$position) {
            usort($position['candidates'], function ($a, $b) {
                return $b['vote_count'] <=> $a['vote_count'];
            });
        }

        return $grouped;
    }

    public function render()
    {
        $groupedResults = $this->getResultsByPosition();

        return view('livewire.real-time-results', [
            'groupedResults' => $groupedResults
        ]);
    }
}
