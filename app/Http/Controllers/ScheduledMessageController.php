<?php

namespace App\Http\Controllers;

use App\Models\ScheduledMessage;
use Illuminate\Http\Request;

class ScheduledMessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = ScheduledMessage::query()
            ->when($request->applied_job_id, fn ($q) => $q->where('applied_job_id', $request->applied_job_id)
            )
            ->latest();
        if ($request->has('status')) {
            $messages->where('status', $request->status);
        }

        $lists = $request->per_page
                ? $messages->paginate($request->per_page)
                : $messages->get();

        return response()->json($lists);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'applied_job_id' => 'required|exists:applied_jobs,id',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
            'description' => 'required|string|max:5000',
        ]);

        $message = ScheduledMessage::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Schedule created successfully',
            'data' => $message,
        ], 201);
    }

    public function show($id)
    {
        $message = ScheduledMessage::findOrFail($id);

        return response()->json($message);
    }

    public function update(Request $request, $id)
    {
        $message = ScheduledMessage::findOrFail($id);

        $data = $request->validate([
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
            'description' => 'required|string|max:5000',
        ]);

        $message->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully',
            'data' => $message,
        ]);
    }

    public function destroy($id)
    {
        ScheduledMessage::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully',
        ]);
    }

    public function statusChange(Request $request ,$id){

        $request->validate([
            'status' => 'required|integer',
        ]);

        $appliedJob = ScheduledMessage::findOrFail($id);

        // Delete the applied job record
        $appliedJob->update([
            'status' =>$request->status
        ]);
        return response()->json(['message' => 'Applied job status successfully.']);
    }
}
