<?php

namespace App\Http\Controllers;

use App\Models\OnsiteSupportTicket;
use App\Http\Requests\OnsiteSupportTicketRequest;
use App\Http\Resources\OnsiteSupportTicketResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class OnsiteSupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = OnsiteSupportTicket::when($request->has('company_name'), function($query) use ($request) {
        return $query->where('company_name', 'like', '%' . $request->company_name . '%');
    })->when($request->status !== null, function($query, $status) {
        return $query->where('status', $status);
    })->orderBy('id', 'desc');

    $lists = $request->per_page
        ? $query->paginate($request->per_page)
        : $query->get();

        return OnsiteSupportTicketResource::collection($lists);
    }


    public function store(OnsiteSupportTicketRequest $request)
    {
        $data = $request->validated();

        $onsiteSupportTicket = OnsiteSupportTicket::create($data);

        
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('uploads/onsiteSupportTicket', 'public');
            $onsiteSupportTicket->update(['attachment' => $path]);
        }
       

        return response()->json([
            'status' => true,
            'message' => 'OnsiteSupportTicket created successfully',
        ], 201);
    }

    public function show(OnsiteSupportTicket $onsiteSupportTicket)
    {
        return new OnsiteSupportTicketResource($onsiteSupportTicket);
    }

    public function update(OnsiteSupportTicketRequest $request, OnsiteSupportTicket $onsiteSupportTicket)
    {
        $data = $request->validated();

        
        if ($request->hasFile('attachment')) {
           
            if ($onsiteSupportTicket->attachment && Storage::disk('public')->exists($onsiteSupportTicket->attachment)) {
                Storage::disk('public')->delete($onsiteSupportTicket->attachment);
            }

            
            $path = $request->file('attachment')->store('uploads/onsiteSupportTicket', 'public');
            $data['attachment'] = $path;
        }
        

        $onsiteSupportTicket->update($data);

        return response()->json([
            'status' => true,
            'message' => 'OnsiteSupportTicket updated successfully',
        ], 200);
    }

    public function destroy(OnsiteSupportTicket $onsiteSupportTicket)
    {
        $onsiteSupportTicket->delete();
        return response()->json(['status' => true,'message' => 'OnsiteSupportTicket deleted successfully'],200);
    }
}
