<?php

namespace App\Http\Controllers;

use App\Models\IssueTicket;
use App\Http\Requests\IssueTicketRequest;
use App\Http\Resources\IssueTicketResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class IssueTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = IssueTicket::with('user')->when($request->status, function($query, $status) {
            return $query->where('status', $status);
                })->when($request->priority_level, function($query, $priority_level) {
                    return $query->where('priority_level', $priority_level);
                })->when($request->issue_type, function($query, $issue_type) {
                    return $query->where('issue_type', $issue_type);
                })->when($request->user_id, function($query, $user_id) {
                    return $query->where('user_id', $user_id);
                })->orderBy('id', 'desc');
        
        $lists = $request->per_page
        ? $query->paginate($request->per_page)
        : $query->get();


        return IssueTicketResource::collection($lists);
    }


    public function store(IssueTicketRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $issueTicket = IssueTicket::create($data);

        
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('uploads/issueTicket', 'public');
            $issueTicket->update(['attachment' => $path]);
        }
       

        return response()->json([
            'status' => true,
            'message' => 'IssueTicket created successfully',
        ], 201);
    }

    public function show(IssueTicket $issueTicket)
    {
        $issueTicket->load('user');
        return new IssueTicketResource($issueTicket);
    }

    public function update(IssueTicketRequest $request, IssueTicket $issueTicket)
    {
        $data = $request->validated();

        
        if ($request->hasFile('image')) {
           
            if ($issueTicket->image && Storage::disk('public')->exists($issueTicket->image)) {
                Storage::disk('public')->delete($issueTicket->image);
            }

            
            $path = $request->file('image')->store('uploads/issueTicket', 'public');
            $data['image'] = $path;
        }
        

        $issueTicket->update($data);

        return response()->json([
            'status' => true,
            'message' => 'IssueTicket updated successfully',
        ], 200);
    }

    public function destroy(IssueTicket $issueTicket)
    {
        $issueTicket->delete();
        return response()->json(['status' => true,'message' => 'IssueTicket deleted successfully'],200);
    }
}
