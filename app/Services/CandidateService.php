<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\CandidatePayment;
use App\Repositories\Interfaces\CandidateRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CandidateService
{
    public function __construct(
        private CandidateRepositoryInterface $candidateRepository
    ) {}

    public function registerCandidate(array $data): Candidate
    {
        return DB::transaction(function () use ($data) {
            // Check if user already registered for this position
            $existingCandidate = Candidate::where('position_id', $data['position_id'])
                ->where('user_id', $data['user_id'])
                ->first();

            if ($existingCandidate) {
                throw new \Exception('User has already registered for this position.');
            }

            // Get the position to determine organization_id
            $position = \App\Models\Position::with('election')->findOrFail($data['position_id']);
            $data['organization_id'] = $position->election->organization_id;

            // Handle photo upload if provided
            if (isset($data['photo'])) {
                $data['photo'] = $this->handlePhotoUpload($data['photo']);
            }

            $data['registered_at'] = now();

            return $this->candidateRepository->create($data);
        });
    }

    public function approveCandidate(int $candidateId, int $approvedBy): bool
    {
        $candidate = $this->candidateRepository->findById($candidateId);

        if (!$candidate) {
            throw new \Exception('Candidate not found.');
        }

        if ($candidate->status !== 'pending') {
            throw new \Exception('Only pending candidates can be approved.');
        }

        // Check if payment is required and confirmed
        if ($candidate->position->amount_required > 0 && !$candidate->payment_confirmed) {
            throw new \Exception('Payment must be confirmed before approval.');
        }

        return $this->candidateRepository->update($candidateId, [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function rejectCandidate(int $candidateId, string $reason): bool
    {
        $candidate = $this->candidateRepository->findById($candidateId);

        if (!$candidate) {
            throw new \Exception('Candidate not found.');
        }

        if ($candidate->status !== 'pending') {
            throw new \Exception('Only pending candidates can be rejected.');
        }

        return $this->candidateRepository->update($candidateId, [
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function processPayment(int $candidateId, array $paymentData): CandidatePayment
    {
        return DB::transaction(function () use ($candidateId, $paymentData) {
            $candidate = $this->candidateRepository->findById($candidateId);

            if (!$candidate) {
                throw new \Exception('Candidate not found.');
            }

            if ($candidate->position->amount_required <= 0) {
                throw new \Exception('No payment required for this position.');
            }

            $payment = CandidatePayment::create([
                'candidate_id' => $candidateId,
                'amount' => $candidate->position->amount_required,
                'status' => 'pending',
                'payment_method' => $paymentData['payment_method'] ?? null,
                'transaction_reference' => $paymentData['transaction_reference'] ?? null,
                'payment_data' => $paymentData,
            ]);

            // In a real application, you would integrate with a payment gateway here
            // For now, we'll simulate payment processing
            $this->simulatePaymentProcessing($payment);

            return $payment;
        });
    }

    public function confirmPayment(int $paymentId): bool
    {
        return DB::transaction(function () use ($paymentId) {
            $payment = CandidatePayment::find($paymentId);

            if (!$payment) {
                throw new \Exception('Payment not found.');
            }

            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            // Update candidate payment confirmation
            $this->candidateRepository->update($payment->candidate_id, [
                'payment_confirmed' => true,
            ]);

            return true;
        });
    }

    public function getCandidatesByPosition(int $positionId, string $status = null): array
    {
        $candidates = $this->candidateRepository->findByPosition($positionId);

        if ($status) {
            $candidates = $candidates->where('status', $status);
        }

        return $candidates->load(['user', 'votes'])->toArray();
    }

    private function handlePhotoUpload($photo): string
    {
        if (is_string($photo)) {
            return $photo; // Already a path
        }

        // Handle file upload
        $path = $photo->store('candidate-photos', 'public');
        return $path;
    }

    private function simulatePaymentProcessing(CandidatePayment $payment): void
    {
        // In a real application, this would integrate with payment gateways
        // For simulation, we'll mark some payments as successful
        if (rand(1, 10) > 2) { // 80% success rate
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'transaction_reference' => 'SIM_' . uniqid(),
            ]);

            $this->candidateRepository->update($payment->candidate_id, [
                'payment_confirmed' => true,
            ]);
        } else {
            $payment->update([
                'status' => 'failed',
                'payment_data' => array_merge($payment->payment_data ?? [], [
                    'error' => 'Simulated payment failure'
                ]),
            ]);
        }
    }
}
