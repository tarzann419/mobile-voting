<?php

namespace App\Http\Controllers;

use App\Models\VoterAccreditation;
use App\Models\Election;
use App\Repositories\Interfaces\VoterAccreditationRepositoryInterface;
use App\Repositories\Interfaces\ElectionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VoterAccreditationController extends Controller
{
    protected VoterAccreditationRepositoryInterface $voterAccreditationRepository;
    protected ElectionRepositoryInterface $electionRepository;

    public function __construct(
        VoterAccreditationRepositoryInterface $voterAccreditationRepository,
        ElectionRepositoryInterface $electionRepository
    ) {
        $this->voterAccreditationRepository = $voterAccreditationRepository;
        $this->electionRepository = $electionRepository;
    }

    /**
     * Display voter accreditations for organization admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;

        // Get all elections for the organization
        $elections = $this->electionRepository->findByOrganization($organizationId);

        // Get accreditations based on filters
        $query = VoterAccreditation::where('organization_id', $organizationId)
            ->with(['user', 'election', 'reviewer']);

        // Filter by election if specified
        if ($request->filled('election_id')) {
            $query->where('election_id', $request->election_id);
        }

        // Filter by status if specified
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $accreditations = $query->orderBy('applied_at', 'desc')->paginate(15);

        return view('voter-accreditation.index', compact('accreditations', 'elections'));
    }

    /**
     * Show form for voter to apply for accreditation
     */
    public function create()
    {
        $user = Auth::user();

        // Get available elections for the user's organization that are accepting registrations
        $elections = $this->electionRepository->findByOrganization($user->organization_id)
            ->filter(function ($election) {
                return $election->isRegistrationOpen();
            });

        return view('voter-accreditation.create', compact('elections'));
    }

    /**
     * Store voter accreditation application
     */
    public function store(Request $request)
    {
        $request->validate([
            'election_id' => 'required|exists:elections,id',
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        // Verify election belongs to user's organization
        $election = Election::where('id', $request->election_id)
            ->where('organization_id', $user->organization_id)
            ->firstOrFail();

        // Check if user already has an accreditation for this election
        $existingAccreditation = $this->voterAccreditationRepository
            ->findByUser($user->id)
            ->where('election_id', $request->election_id)
            ->first();

        if ($existingAccreditation) {
            return redirect()->back()->withErrors(['election_id' => 'You have already applied for accreditation for this election.']);
        }

        // Handle document uploads
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                $path = $file->store('voter-accreditation-documents', 'public');
                $documents[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getClientMimeType(),
                ];
            }
        }

        // Create accreditation application
        $this->voterAccreditationRepository->create([
            'organization_id' => $user->organization_id,
            'election_id' => $request->election_id,
            'user_id' => $user->id,
            'status' => 'pending',
            'documents' => $documents,
            'applied_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Your voter accreditation application has been submitted successfully.');
    }

    /**
     * Approve voter accreditation
     */
    public function approve(Request $request, VoterAccreditation $accreditation)
    {
        $user = Auth::user();

        // Verify accreditation belongs to user's organization
        if ($accreditation->organization_id !== $user->organization_id) {
            abort(403);
        }

        $request->validate([
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        $this->voterAccreditationRepository->update($accreditation->id, [
            'status' => 'approved',
            'verification_notes' => $request->verification_notes,
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
        ]);

        return redirect()->route('voter-accreditation.index')
            ->with('success', 'Voter accreditation approved successfully.');
    }

    /**
     * Reject voter accreditation
     */
    public function reject(Request $request, VoterAccreditation $accreditation)
    {
        $user = Auth::user();

        // Verify accreditation belongs to user's organization
        if ($accreditation->organization_id !== $user->organization_id) {
            abort(403);
        }

        $request->validate([
            'verification_notes' => 'required|string|max:1000',
        ]);

        $this->voterAccreditationRepository->update($accreditation->id, [
            'status' => 'rejected',
            'verification_notes' => $request->verification_notes,
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
        ]);

        return redirect()->route('voter-accreditation.index')
            ->with('success', 'Voter accreditation rejected successfully.');
    }
}
