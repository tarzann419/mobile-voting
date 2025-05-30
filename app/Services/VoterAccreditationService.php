<?php

namespace App\Services;

use App\Models\VoterAccreditation;
use App\Repositories\Interfaces\VoterAccreditationRepositoryInterface;
use App\Repositories\Interfaces\ElectionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VoterAccreditationService
{
    public function __construct(
        private VoterAccreditationRepositoryInterface $voterAccreditationRepository,
        private ElectionRepositoryInterface $electionRepository
    ) {}

    public function applyForAccreditation(array $data): VoterAccreditation
    {
        return DB::transaction(function () use ($data) {
            $election = $this->electionRepository->findById($data['election_id']);

            if (!$election) {
                throw new \Exception('Election not found.');
            }

            if (!$election->isRegistrationOpen()) {
                throw new \Exception('Voter registration is not currently open for this election.');
            }

            // Check if user already applied
            $existingApplication = VoterAccreditation::where('election_id', $data['election_id'])
                ->where('user_id', $data['user_id'])
                ->first();

            if ($existingApplication) {
                throw new \Exception('User has already applied for accreditation in this election.');
            }

            // Handle document uploads
            if (isset($data['documents'])) {
                $data['documents'] = $this->handleDocumentUploads($data['documents']);
            }

            $data['applied_at'] = now();

            return $this->voterAccreditationRepository->create($data);
        });
    }

    public function approveAccreditation(int $accreditationId, int $reviewedBy, string $notes = null): bool
    {
        return DB::transaction(function () use ($accreditationId, $reviewedBy, $notes) {
            $accreditation = $this->voterAccreditationRepository->findById($accreditationId);

            if (!$accreditation) {
                throw new \Exception('Accreditation not found.');
            }

            if ($accreditation->status !== 'pending') {
                throw new \Exception('Only pending accreditations can be approved.');
            }

            return $this->voterAccreditationRepository->update($accreditationId, [
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $reviewedBy,
                'verification_notes' => $notes,
            ]);
        });
    }

    public function rejectAccreditation(int $accreditationId, int $reviewedBy, string $reason): bool
    {
        return DB::transaction(function () use ($accreditationId, $reviewedBy, $reason) {
            $accreditation = $this->voterAccreditationRepository->findById($accreditationId);

            if (!$accreditation) {
                throw new \Exception('Accreditation not found.');
            }

            if ($accreditation->status !== 'pending') {
                throw new \Exception('Only pending accreditations can be rejected.');
            }

            return $this->voterAccreditationRepository->update($accreditationId, [
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => $reviewedBy,
                'verification_notes' => $reason,
            ]);
        });
    }

    public function getAccreditationsByElection(int $electionId, string $status = null): array
    {
        $accreditations = $this->voterAccreditationRepository->findByElection($electionId);

        if ($status) {
            $accreditations = $accreditations->where('status', $status);
        }

        return $accreditations->load(['user', 'reviewer'])->toArray();
    }

    public function getUserAccreditationStatus(int $userId, int $electionId): array
    {
        $accreditation = VoterAccreditation::where('user_id', $userId)
            ->where('election_id', $electionId)
            ->first();

        if (!$accreditation) {
            return [
                'status' => 'not_applied',
                'message' => 'User has not applied for accreditation.',
                'accreditation' => null
            ];
        }

        $messages = [
            'pending' => 'Application is under review.',
            'approved' => 'User is accredited to vote.',
            'rejected' => 'Application was rejected. Reason: ' . $accreditation->verification_notes
        ];

        return [
            'status' => $accreditation->status,
            'message' => $messages[$accreditation->status] ?? 'Unknown status.',
            'accreditation' => $accreditation
        ];
    }

    public function bulkApproveAccreditations(array $accreditationIds, int $reviewedBy): array
    {
        $results = [];

        foreach ($accreditationIds as $id) {
            try {
                $this->approveAccreditation($id, $reviewedBy);
                $results['approved'][] = $id;
            } catch (\Exception $e) {
                $results['failed'][] = ['id' => $id, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    private function handleDocumentUploads(array $documents): array
    {
        $uploadedPaths = [];

        foreach ($documents as $document) {
            if (is_string($document)) {
                $uploadedPaths[] = $document; // Already a path
                continue;
            }

            // Handle file upload
            $path = $document->store('voter-documents', 'public');
            $uploadedPaths[] = $path;
        }

        return $uploadedPaths;
    }
}
