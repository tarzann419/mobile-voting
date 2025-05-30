<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'position_id',
        'user_id',
        'bio',
        'manifesto',
        'photo',
        'status',
        'payment_confirmed',
        'registered_at',
        'approved_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'payment_confirmed' => 'boolean',
            'registered_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function payments()
    {
        return $this->hasMany(CandidatePayment::class);
    }

    public function getVoteCountAttribute()
    {
        return $this->votes()->count();
    }

    public function getVotePercentageAttribute()
    {
        $totalVotes = $this->position->vote_count;
        return $totalVotes > 0 ? round(($this->vote_count / $totalVotes) * 100, 2) : 0;
    }

    public function isEligible()
    {
        return $this->status === 'approved' &&
            ($this->position->amount_required == 0 || $this->payment_confirmed);
    }

    public function getLatestPaymentAttribute()
    {
        return $this->payments()->latest()->first();
    }
}
