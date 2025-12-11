<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AssetMutationService;
use App\Services\AssetDeletionService;
use App\Models\AssetMutation;
use App\Models\AssetDeletion;
use App\Models\Asset;
use App\Models\OpdUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProposalController extends Controller
{
    private AssetMutationService $mutationService;
    private AssetDeletionService $deletionService;

    public function __construct(
        AssetMutationService $mutationService,
        AssetDeletionService $deletionService
    ) {
        $this->middleware(['auth', 'admin.utama']);
        $this->mutationService = $mutationService;
        $this->deletionService = $deletionService;
    }

    /**
     * Display all proposals (mutations & deletions)
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'mutations'); // mutations, deletions
        $status = $request->get('status');
        $perPage = $request->get('per_page', 20);
        
        if ($type === 'mutations') {
            $query = AssetMutation::with(['asset', 'fromOpdUnit', 'toOpdUnit', 'mutator'])
                ->orderBy('mutation_date', 'desc');
            
            $title = 'Proposal Mutasi Aset';
            $statuses = ['diusulkan', 'disetujui', 'selesai', 'ditolak'];
        } else {
            $query = AssetDeletion::with(['asset', 'proposer', 'verifier', 'approver'])
                ->orderBy('proposed_at', 'desc');
            
            $title = 'Proposal Penghapusan Aset';
            $statuses = AssetDeletion::STATUSES;
        }
        
        if ($status && in_array($status, $statuses)) {
            $query->where('status', $status);
        }
        
        $proposals = $query->paginate($perPage);
        
        return view('admin.proposals.index', [
            'title' => $title,
            'proposals' => $proposals,
            'type' => $type,
            'status' => $status,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Display proposal detail
     */
    public function show(Request $request, $type, $id)
    {
        $tab = $request->get('tab', 'detail');
        $allowedTabs = ['detail', 'timeline', 'documents'];
        
        if ($type === 'mutation') {
            $proposal = AssetMutation::with([
                'asset.category', 'asset.opdUnit', 'asset.location',
                'fromOpdUnit', 'toOpdUnit', 'fromLocation', 'toLocation', 'mutator'
            ])->findOrFail($id);
            
            $service = $this->mutationService;
            $timeline = []; // Mutation timeline bisa ditambahkan jika diperlukan
        } else {
            $proposal = AssetDeletion::with([
                'asset.category', 'asset.opdUnit', 'asset.location',
                'proposer', 'verifier', 'approver'
            ])->findOrFail($id);
            
            $service = $this->deletionService;
            $timeline = $service->getTimeline($proposal);
        }
        
        return view('admin.proposals.show', [
            'title' => 'Detail Proposal ' . ($type === 'mutation' ? 'Mutasi' : 'Penghapusan'),
            'proposal' => $proposal,
            'type' => $type,
            'tab' => $tab,
            'timeline' => $timeline,
            'allowedTabs' => $allowedTabs,
        ]);
    }

    /**
     * Verify proposal (for deletions only)
     */
    public function verify(Request $request, $type, $id)
    {
        if ($type !== 'deletion') {
            return redirect()->back()->with('error', 'Hanya proposal penghapusan yang perlu diverifikasi');
        }

        try {
            $this->deletionService->verifyProposal($id, auth()->id());
            
            return redirect()
                ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                ->with('success', 'Proposal penghapusan berhasil diverifikasi');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                ->with('error', 'Gagal memverifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Approve proposal
     */
    public function approve(Request $request, $type, $id)
    {
        $request->validate([
            'approval_documents' => 'nullable|array',
            'approval_documents.*' => 'nullable|string',
        ]);

        try {
            if ($type === 'mutation') {
                $this->mutationService->approveMutation($id);
            } else {
                $this->deletionService->approveProposal(
                    $id,
                    auth()->id(),
                    $request->approval_documents ?? []
                );
            }
            
            return redirect()
                ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                ->with('success', 'Proposal berhasil disetujui');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                ->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    /**
     * Complete proposal process
     */
    public function complete(Request $request, $type, $id)
    {
        if ($type === 'mutation') {
            try {
                $this->mutationService->completeMutation($id);
                
                return redirect()
                    ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                    ->with('success', 'Proses mutasi berhasil diselesaikan');
            } catch (\Exception $e) {
                return redirect()
                    ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                    ->with('error', 'Gagal menyelesaikan: ' . $e->getMessage());
            }
        } else {
            $request->validate([
                'deletion_method' => 'required|in:' . implode(',', \App\Models\AssetDeletion::DELETION_METHODS),
                'sale_value' => 'nullable|numeric|min:0',
                'recipient' => 'nullable|string|max:255',
                'completion_notes' => 'nullable|string|max:500',
            ]);

            try {
                $details = [
                    'sale_value' => $request->sale_value,
                    'recipient' => $request->recipient,
                    'notes' => $request->completion_notes,
                ];
                
                $this->deletionService->completeDeletion(
                    $id,
                    $request->deletion_method,
                    $details
                );
                
                return redirect()
                    ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                    ->with('success', 'Proses penghapusan berhasil diselesaikan');
            } catch (\Exception $e) {
                return redirect()
                    ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                    ->with('error', 'Gagal menyelesaikan: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reject proposal
     */
    public function reject(Request $request, $type, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        try {
            if ($type === 'mutation') {
                $this->mutationService->rejectMutation($id, $request->rejection_reason);
            } else {
                $this->deletionService->rejectProposal($id, $request->rejection_reason);
            }
            
            return redirect()
                ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                ->with('success', 'Proposal berhasil ditolak');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                ->with('error', 'Gagal menolak: ' . $e->getMessage());
        }
    }

    /**
     * Cancel proposal
     */
    public function cancel(Request $request, $type, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|min:10|max:500',
        ]);

        try {
            // Only deletions can be cancelled
            if ($type === 'deletion') {
                $this->deletionService->cancelProposal($id, $request->cancellation_reason);
                
                return redirect()
                    ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                    ->with('success', 'Proposal penghapusan berhasil dibatalkan');
            } else {
                return redirect()
                    ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                    ->with('error', 'Hanya proposal penghapusan yang dapat dibatalkan');
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.proposals.show', ['type' => $type, 'id' => $id])
                ->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }

    /**
     * Display bulk approval form
     */
    public function bulkApproval(Request $request)
    {
        $type = $request->get('type', 'mutations'); // mutations, deletions
        
        if ($type === 'mutations') {
            $proposals = AssetMutation::with(['asset', 'fromOpdUnit', 'toOpdUnit'])
                ->where('status', 'diusulkan')
                ->orderBy('mutation_date', 'desc')
                ->get();
                
            $title = 'Persetujuan Massal Mutasi';
        } else {
            $proposals = AssetDeletion::with(['asset', 'proposer'])
                ->where('status', AssetDeletion::STATUS_DIUSULKAN)
                ->whereNotNull('verified_by')
                ->orderBy('proposed_at', 'desc')
                ->get();
                
            $title = 'Persetujuan Massal Penghapusan';
        }
        
        return view('admin.proposals.bulk-approval', [
            'title' => $title,
            'proposals' => $proposals,
            'type' => $type,
        ]);
    }

    /**
     * Process bulk approval
     */
    public function processBulkApproval(Request $request)
    {
        $request->validate([
            'type' => 'required|in:mutations,deletions',
            'proposal_ids' => 'required|array',
            'proposal_ids.*' => 'exists:' . ($request->type === 'mutations' ? 'asset_mutations,mutation_id' : 'asset_deletions,deletion_id'),
        ]);

        DB::beginTransaction();
        try {
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($request->proposal_ids as $proposalId) {
                try {
                    if ($request->type === 'mutations') {
                        $this->mutationService->approveMutation($proposalId);
                    } else {
                        $this->deletionService->approveProposal($proposalId, auth()->id(), []);
                    }
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Proposal {$proposalId}: " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            $message = "Berhasil menyetujui {$successCount} proposal";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} gagal";
                session()->flash('errors', $errors);
            }
            
            return redirect()
                ->route('admin.proposals.index', ['type' => $request->type, 'status' => 'diusulkan'])
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.proposals.bulk-approval', ['type' => $request->type])
                ->with('error', 'Gagal menyetujui massal: ' . $e->getMessage());
        }
    }
}