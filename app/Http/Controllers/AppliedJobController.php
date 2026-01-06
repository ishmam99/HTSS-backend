<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppliedJobResource;
use App\Models\AppliedJob;
use Illuminate\Http\Request;

class AppliedJobController extends Controller
{
    /**
     * Display a listing of applied jobs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $appliedJobs = AppliedJob::with(['job', 'software', 'industries']);

        if ($request->has('job_status') && $request->job_status == 'not_null') {
            $appliedJobs->whereNotNull('job_id');
        } elseif ($request->has('job_status') && $request->job_status == 'null') {
            $appliedJobs->whereNull('job_id');
        }
        $appliedJobs = $appliedJobs->get();

        // Return the collection of AppliedJob resources
        return AppliedJobResource::collection($appliedJobs);
    }

    /**
     * Display the specified applied job.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Retrieve a specific applied job with relationships
        $appliedJob = AppliedJob::with(['job', 'software', 'industries'])->findOrFail($id);

        // Return the specific AppliedJob resource
        return new AppliedJobResource($appliedJob);
    }

    /**
     * Store a newly created applied job in the database.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'system' => 'nullable|string|max:255',
            'softwares' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'highest_education' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'resume' => 'nullable|file|mimes:pdf|max:10240',
            'job_id' => 'nullable|exists:job_offers,id',
            'software_id' => 'nullable|exists:softwares,id',
            'industry_id' => 'nullable|exists:industries,id',
        ]);

        if ($request->hasFile('resume')) {
            $validated['resume'] = $request->file('resume')->store('resume', 'public');
        }

        $appliedJob = AppliedJob::create($validated);

        return response()->json([
            'message' => 'Applied job created successfully',
            'data' => $appliedJob,
        ], 201);
    }


    /**
     * Update the specified applied job in the database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'system' => 'nullable|string|max:255',
            'highest_education' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'resume' => 'nullable|file|mimes:pdf|max:10240',
            'job_id' => 'nullable|exists:job_offers,id',
            'software_id' => 'nullable|exists:softwares,id',
            'industry_id' => 'nullable|exists:industries,id',
        ]);

        // Find the applied job by ID
        $appliedJob = AppliedJob::findOrFail($id);

        // Check if a new PDF resume has been uploaded and update
        if ($request->hasFile('resume')) {
            $pdfPath = $request->file('resume')->store('resume');
            $appliedJob->resume = $pdfPath;
        }

        // Update the applied job data
        $appliedJob->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'contact' => $request->contact,
            'emergency_contact' => $request->emergency_contact,
            'system' => $request->system,
            'softwares' => $request->softwares,
            'industry' => $request->industry,
            'highest_education' => $request->highest_education,
            'university' => $request->university,
            'job_id' => $request->job_id,
            'software_id' => $request->software_id,
            'industry_id' => $request->industry_id,
        ]);

        // Return the updated AppliedJob resource
        return new AppliedJobResource($appliedJob);
    }

    /**
     * Remove the specified applied job from the database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find the applied job by ID
        $appliedJob = AppliedJob::findOrFail($id);

        // Delete the applied job record
        $appliedJob->delete();

        // Return a success message
        return response()->json(['message' => 'Applied job deleted successfully.']);
    }
}
