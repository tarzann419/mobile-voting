<?php

namespace App\Repositories;

use App\Models\Vote;
use App\Repositories\Interfaces\VoteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class VoteRepository implements VoteRepositoryInterface
{
    public function findById(int $id): ?Vote
    {
        return Vote::find($id);
    }

    public function findByElection(int $electionId): Collection
    {
        return Vote::where('election_id', $electionId)->get();
    }

    public function findByUser(int $userId): Collection
    {
        return Vote::where('user_id', $userId)->get();
    }

    public function create(array $data): Vote
    {
        return Vote::create($data);
    }

    public function hasUserVotedForPosition(int $userId, int $positionId): bool
    {
        return Vote::where('user_id', $userId)
            ->where('position_id', $positionId)
            ->exists();
    }

    public function getVoteCountByCandidate(int $candidateId): int
    {
        return Vote::where('candidate_id', $candidateId)->count();
    }

    public function getVotesByPosition(int $positionId): Collection
    {
        return Vote::where('position_id', $positionId)->get();
    }

    public function getRealTimeResults(int $electionId): array
    {
        return DB::select("
            SELECT 
                p.id as position_id,
                p.title as position_title,
                c.id as candidate_id,
                u.name as candidate_name,
                COUNT(v.id) as vote_count,
                ROUND(COUNT(v.id) * 100.0 / NULLIF(position_totals.total_votes, 0), 2) as percentage
            FROM positions p
            LEFT JOIN candidates c ON p.id = c.position_id AND c.status = 'approved'
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN votes v ON c.id = v.candidate_id
            LEFT JOIN (
                SELECT 
                    p2.id as position_id,
                    COUNT(v2.id) as total_votes
                FROM positions p2
                LEFT JOIN candidates c2 ON p2.id = c2.position_id
                LEFT JOIN votes v2 ON c2.id = v2.candidate_id
                WHERE p2.election_id = ?
                GROUP BY p2.id
            ) position_totals ON p.id = position_totals.position_id
            WHERE p.election_id = ?
            GROUP BY p.id, p.title, c.id, u.name, position_totals.total_votes
            ORDER BY p.order, vote_count DESC
        ", [$electionId, $electionId]);
    }
}
