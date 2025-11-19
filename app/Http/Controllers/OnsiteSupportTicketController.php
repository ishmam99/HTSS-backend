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
        $query = OnsiteSupportTicket::advancedQuery($request);
    $lists = $request->per_page
        ? $query->paginate($request->per_page)
        : $query->get();

        return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => OnsiteSupportTicket::count()
        ]);
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
