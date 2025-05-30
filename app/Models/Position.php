<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'election_id',
        'title',
        'description',
        'amount_required',
        'max_candidates',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'amount_required' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function approvedCandidates()
    {
        return $this->hasMany(Candidate::class)->where('status', 'approved');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getVoteCountAttribute()
    {
        return $this->votes()->count();
    }

    public function getCandidatesCountAttribute()
    {
        return $this->candidates()->count();
    }

    public function getApprovedCandidatesCountAttribute()
    {
        return $this->approvedCandidates()->count();
    }
}
