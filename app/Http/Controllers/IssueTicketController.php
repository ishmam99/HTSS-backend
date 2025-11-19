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
        $query = IssueTicket::advancedQuery($request);
        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => IssueTicket::count()
        ]);
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


        if ($request->hasFile('attachment')) {

            if ($issueTicket->attachment && Storage::disk('public')->exists($issueTicket->attachment)) {
                Storage::disk('public')->delete($issueTicket->attachment);
            }


            $path = $request->file('attachment')->store('uploads/issueTicket', 'public');
            $data['attachment'] = $path;
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
