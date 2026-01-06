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
        $appliedJobs = AppliedJob::with(['job', 'software', 'industry']);

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
        $appliedJob = AppliedJob::with(['job', 'software', 'industry'])->findOrFail($id);

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
        // Validate incoming request data
        if ($request->hasFile('resume')) {
            // Validation rules when pdf_resume is uploaded
            $request->validate([
                'full_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'contact' => 'nullable|string|max:20',
                'emergency_contact' => 'nullable|string|max:20',
                'system' => 'nullable|string|max:255',
                'highest_education' => 'nullable|string|max:255',
                'university' => 'nullable|string|max:255',
                'resume' => 'required|file|mimes:pdf|max:10240',
                'job_id' => 'nullable|exists:job_offers,id',
                'software_id' => 'nullable|exists:softwares,id',
                'industry_id' => 'nullable|exists:industries,id',
            ]);
        } else {
            // Validation rules when no pdf_resume is uploaded
            $request->validate([
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
                'job_id' => 'nullable|exists:jobs,id',
                'software_id' => 'required|exists:softwares,id',
                'industry_id' => 'required|exists:industries,id',
            ]);
        }

        // Store the PDF resume if uploaded
        $pdfPath = null;
        if ($request->hasFile('resume')) {
            $pdfPath = $request->file('resume')->store('resume');
        }

        // Create the new AppliedJob entry in the database
        $appliedJob = AppliedJob::create([
            'full_name' => $request->full_name ?? null,
            'email' => $request->email ?? null,
            'contact' => $request->contact ?? null,
            'emergency_contact' => $request->emergency_contact ?? null,
            'system' => $request->system ?? null,
            'softwares' => $request->softwares ?? null,
            'industry' => $request->industry ?? null,
            'highest_education' => $request->highest_education ?? null,
            'university' => $request->university ?? null,
            'resume' => $pdfPath, // Store the path to the uploaded file if any
            'job_id' => $request->job_id ?? null,
            'software_id' => $request->software_id ?? null,
            'industry_id' => $request->industry_id ?? null,
        ]);

        // Return the newly created AppliedJob resource
        return new AppliedJobResource($appliedJob);
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
