<?php

namespace App\Livewire;

use App\Services\ElectionService;
use App\Services\VotingService;
use App\Repositories\Interfaces\ElectionRepositoryInterface;
use Livewire\Component;
use Livewire\Attributes\On;

class ElectionDashboard extends Component
{
    public $electionId;
    public $election;
    public $stats = [];
    public $results = [];

    public function __construct(
        private ElectionService $electionService,
        private VotingService $votingService,
        private ElectionRepositoryInterface $electionRepository
    ) {}

    public function mount($electionId)
    {
        $this->electionId = $electionId;
        $this->loadElection();
        $this->loadStats();
        $this->loadResults();
    }

    public function loadElection()
    {
        $this->election = $this->electionService->getElectionWithDetails($this->electionId);

        if (!$this->election) {
            session()->flash('error', 'Election not found.');
            return;
        }

        // Check authorization
        if (auth()->user()->organization_id !== $this->election->organization_id) {
            session()->flash('error', 'Unauthorized access.');
            return;
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

    public function loadResults()
    {
        try {
            $this->results = $this->votingService->getRealTimeResults($this->electionId);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function publishElection()
    {
        try {
            $this->electionService->publishElection($this->electionId);
            $this->loadElection();
            session()->flash('success', 'Election published successfully.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function startElection()
    {
        try {
            $this->electionService->startElection($this->electionId);
            $this->loadElection();
            session()->flash('success', 'Election started successfully.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function endElection()
    {
        try {
            $this->electionService->endElection($this->electionId);
            $this->loadElection();
            session()->flash('success', 'Election ended successfully.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    #[On('vote-cast')]
    public function refreshData()
    {
        $this->loadStats();
        $this->loadResults();
    }

    public function render()
    {
        return view('livewire.election-dashboard');
    }
}
