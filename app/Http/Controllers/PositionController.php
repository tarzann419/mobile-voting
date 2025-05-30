<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Build the query for positions
        $positionsQuery = Position::whereHas('election', function ($query) use ($user) {
            $query->where('organization_id', $user->organization_id);
        })->with('election');

        // Filter by election if specified
        if ($request->filled('election_id')) {
            $positionsQuery->where('election_id', $request->election_id);
        }

        $positions = $positionsQuery->orderBy('created_at', 'desc')->paginate(15);

        // Get available elections for the dropdown
        $elections = Election::where('organization_id', $user->organization_id)
            ->where('status', '!=', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('positions.index', compact('positions', 'elections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'election_id' => 'required|exists:elections,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_candidates' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
            'registration_fee' => 'nullable|numeric|min:0',
        ]);

        // Verify the election belongs to the user's organization
        $election = Election::where('id', $request->election_id)
            ->where('organization_id', Auth::user()->organization_id)
            ->firstOrFail();

        Position::create([
            'organization_id' => Auth::user()->organization_id,
            'election_id' => $request->election_id,
            'title' => $request->title,
            'description' => $request->description,
            'max_candidates' => $request->max_candidates,
            'order' => $request->order,
            'amount_required' => $request->registration_fee ?? 0,
        ]);

        return redirect()->route('positions.index')
            ->with('success', 'Position created successfully.');
    }

    public function edit(Position $position)
    {
        // Verify the position belongs to the user's organization
        $position->load('election');
        if ($position->election->organization_id !== Auth::user()->organization_id) {
            abort(403);
        }

        $elections = Election::where('organization_id', Auth::user()->organization_id)
            ->where('status', '!=', 'completed')
            ->get();

        return view('positions.edit', compact('position', 'elections'));
    }

    public function update(Request $request, Position $position)
    {
        // Verify the position belongs to the user's organization
        $position->load('election');
        if ($position->election->organization_id !== Auth::user()->organization_id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_candidates' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
            'registration_fee' => 'nullable|numeric|min:0',
        ]);

        $position->update([
            'title' => $request->title,
            'description' => $request->description,
            'max_candidates' => $request->max_candidates,
            'order' => $request->order,
            'amount_required' => $request->registration_fee ?? 0,
        ]);

        return redirect()->route('positions.index')
            ->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        // Verify the position belongs to the user's organization
        $position->load('election');
        if ($position->election->organization_id !== Auth::user()->organization_id) {
            abort(403);
        }

        // Check if position has candidates
        if ($position->candidates()->count() > 0) {
            return redirect()->route('positions.index')
                ->with('error', 'Cannot delete position with existing candidates.');
        }

        $position->delete();

        return redirect()->route('positions.index')
            ->with('success', 'Position deleted successfully.');
    }
}
