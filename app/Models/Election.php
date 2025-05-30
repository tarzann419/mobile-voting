<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'status',
        'registration_start_date',
        'registration_end_date',
        'voting_start_date',
        'voting_end_date',
        'allow_multiple_votes',
        'require_payment',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'registration_start_date' => 'datetime',
            'registration_end_date' => 'datetime',
            'voting_start_date' => 'datetime',
            'voting_end_date' => 'datetime',
            'allow_multiple_votes' => 'boolean',
            'require_payment' => 'boolean',
            'settings' => 'array',
        ];
    }

    // Add date accessors for dashboard compatibility
    public function getStartDateAttribute()
    {
        return $this->voting_start_date;
    }

    public function getEndDateAttribute()
    {
        return $this->voting_end_date;
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function candidates()
    {
        return $this->hasManyThrough(Candidate::class, Position::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function voterAccreditations()
    {
        return $this->hasMany(VoterAccreditation::class);
    }

    public function isRegistrationOpen()
    {
        $now = now();
        return $this->status === 'published' &&
            $now >= $this->registration_start_date &&
            $now <= $this->registration_end_date;
    }

    public function isVotingActive()
    {
        $now = now();
        return $this->status === 'active' &&
            $now >= $this->voting_start_date &&
            $now <= $this->voting_end_date;
    }

    public function getTotalVotesAttribute()
    {
        return $this->votes()->count();
    }

    public function getAccreditedVotersAttribute()
    {
        return $this->voterAccreditations()->where('status', 'approved')->count();
    }

    public function userHasVoted($userId)
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }
}
