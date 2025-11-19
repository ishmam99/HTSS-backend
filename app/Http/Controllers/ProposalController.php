<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Http\Requests\ProposalRequest;
use App\Http\Resources\ProposalResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
       $query = Proposal::with(['account','deal','createdBy','updatedBy'])
        ->when($request->status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->when($request->account_id, function ($query, $accountId) {
            return $query->where('account_id', $accountId);
        })
        ->when($request->deal_id, function ($query, $dealId) {
            return $query->where('deal_id', $dealId);
        })
        ->orderBy('id', 'desc');

    $lists = $request->has('per_page')
        ? $query->paginate($request->per_page)
        : $query->get();

        return ProposalResource::collection($lists);
    }


    public function store(ProposalRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();
        $data['terms_and_conditions'] = json_encode($data['terms_and_conditions'] ?? []);
        $data['special_terms_and_conditions'] = json_encode($data['special_terms_and_conditions'] ?? []);
        $proposal = Proposal::create($data);
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('uploads/proposal', 'public');
            $proposal->update(['attachment' => $path]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Proposal created successfully',
        ], 201);
    }

    public function show(Proposal $proposal)
    {
        $proposal->load(['account','deal','createdBy','updatedBy']);
        return new ProposalResource($proposal);
    }

    public function update(ProposalRequest $request, Proposal $proposal)
    {
        $data = $request->validated();
        $data['terms_and_conditions'] =  json_encode($data['terms_and_conditions']) ?? $proposal->terms_and_conditions; ;
        $data['special_terms_and_conditions'] = json_encode($data['special_terms_and_conditions']) ?? $proposal->special_terms_and_conditions; ;
        
        if ($request->hasFile('image')) {
           
            if ($proposal->image && Storage::disk('public')->exists($proposal->image)) {
                Storage::disk('public')->delete($proposal->image);
            }

            
            $path = $request->file('image')->store('uploads/proposal', 'public');
            $data['image'] = $path;
        }
        

        $proposal->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Proposal updated successfully',
        ], 200);
    }

    public function destroy(Proposal $proposal)
    {
        if ($proposal->attachment && Storage::disk('public')->exists($proposal->attachment)) {
            Storage::disk('public')->delete($proposal->attachment);
        }
        $proposal->delete();
        return response()->json(['status' => true,'message' => 'Proposal deleted successfully'],200);
    }

    public function updateStatus($dealId, $proposalId, Request $request)
    {
        $proposal = Proposal::where('id', $proposalId)
            ->firstOrFail();

            $proposal->update([
            'status' => 1,
            ]);
        $proposal->where('deal_id', $dealId)
            ->where('id', '!=', $proposal->id)
            ->update(['status' => 0]);
           
        return response()->json([
            'status' => true,
            'message' => 'Proposal status updated successfully',
        ], 200);
    }
}
