<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppliedJobResource;
use App\Models\AppliedJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AppliedJobController extends Controller
{
    /**
     * List applied jobs (HR only ideally)
     */
    public function index(Request $request)
    {
        $appliedJobs = AppliedJob::with(['job', 'software', 'industries']);

        if ($request->job_status === 'not_null') {
            $appliedJobs->whereNotNull('job_id');
        } elseif ($request->job_status === 'null') {
            $appliedJobs->whereNull('job_id');
        }

        if ($request->has('status')) {
            $appliedJobs->where('status', $request->status);
        }

        return AppliedJobResource::collection($appliedJobs->latest()->get());
    }

    /**
     * Show single
     */
    public function show($id)
    {
        $appliedJob = AppliedJob::with(['job', 'software', 'industries'])->findOrFail($id);
        return new AppliedJobResource($appliedJob);
    }

    /**
     * STORE (Public Applicant)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',

            'highest_education' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',

            'resume' => 'nullable|file|mimes:pdf|max:10240',
            'link' => 'nullable|string|max:255',

            // references (applicant allowed)
            'reference_one_name' => 'nullable|string|max:255',
            'reference_one_number' => 'nullable|string|max:20',
            'reference_one_designation' => 'nullable|string|max:255',
            'reference_one_email' => 'nullable|email|max:255',

            'reference_two_name' => 'nullable|string|max:255',
            'reference_two_number' => 'nullable|string|max:20',
            'reference_two_designation' => 'nullable|string|max:255',
            'reference_two_email' => 'nullable|email|max:255',

            // signature
            'signature' => 'nullable|file|mimes:png,jpg,jpeg|max:5120',

            'job_id' => 'nullable|exists:job_offers,id',
        ]);

        // File uploads
        if ($request->hasFile('resume')) {
            $validated['resume'] = $request->file('resume')->store('resume', 'public');
        }

        if ($request->hasFile('signature')) {
            $validated['signature_path'] = $request->file('signature')->store('signatures', 'public');
            $validated['signature_uploaded'] = true;
        }

        // Default flags
        $validated['terms_accepted'] = true;

        AppliedJob::create($validated);

        return response()->json([
            'message' => 'Application submitted successfully',
        ], 201);
    }

    /**
     * UPDATE (HR + Applicant partial)
     */
    public function update(Request $request, $id)
    {
        $appliedJob = AppliedJob::findOrFail($id);

        // Assume frontend sends role flag OR use auth later
        $isHR = auth()->user()->role == 'hr-director' || auth()->user()->role == 'hr-manager' || auth()->user()->role == 'hr-executive' || auth()->user()->role == 'hr-vp' ? true : false;

        if ($isHR) {
            // 🧑‍💼 HR VALIDATION
            $validated = $request->validate([
                'technical_skills' => 'nullable|integer|min:1|max:10',
                'communication' => 'nullable|integer|min:1|max:10',
                'cultural_fit' => 'nullable|integer|min:1|max:10',
                'problem_solving' => 'nullable|integer|min:1|max:10',

                'overall_comment' => 'nullable|string',
                'recommendation' => 'nullable|in:hire,no_hire,hold',

                'status' => 'nullable|integer',

                'reference_checked' => 'nullable|boolean',
                'background_verified' => 'nullable|boolean',
                'documents_verified' => 'nullable|boolean',

                'expected_salary' => 'nullable|numeric|min:0',
            ]);
        } else {
            // 👤 APPLICANT UPDATE (LIMITED)
            $validated = $request->validate([
                'contact' => 'nullable|string|max:20',
                'address' => 'nullable|string',

                'reference_one_name' => 'nullable|string|max:255',
                'reference_one_number' => 'nullable|string|max:20',
                'reference_one_designation' => 'nullable|string|max:255',
                'reference_one_email' => 'nullable|email|max:255',

                'reference_two_name' => 'nullable|string|max:255',
                'reference_two_number' => 'nullable|string|max:20',
                'reference_two_designation' => 'nullable|string|max:255',
                'reference_two_email' => 'nullable|email|max:255',
            ]);
        }

        // Resume update
        if ($request->hasFile('resume')) {
            $validated['resume'] = $request->file('resume')->store('resume', 'public');
        }

        // Signature update
        if ($request->hasFile('signature')) {
            $validated['signature_path'] = $request->file('signature')->store('signatures', 'public');
            $validated['signature_uploaded'] = true;
        }

        $appliedJob->update($validated);

        return new AppliedJobResource($appliedJob);
    }

    public function generateAccessLink($id)
    {
        $appliedJob = AppliedJob::findOrFail($id);

        $token = Str::random(64);

        $appliedJob->update([
            'access_token' => $token,
            'access_token_expires_at' => Carbon::now()->addDays(3), // 3 days validity
        ]);

        $link = url("/applicant-access/{$token}");

        return response()->json([
            'link' => $link,
            'expires_at' => $appliedJob->access_token_expires_at
        ]);
    }
    public function destroy($id)
    {
        AppliedJob::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }

    /**
     * HR Status Change Only
     */
    public function statusChange(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer',
        ]);

        $appliedJob = AppliedJob::findOrFail($id);

        $appliedJob->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Status updated successfully'
        ]);
    }
    public function accessByToken($token)
    {
        $appliedJob = AppliedJob::where('access_token', $token)
            ->with('job')
            ->where('access_token_expires_at', '>', now())
            ->first();

        if (!$appliedJob) {
            return response()->json([
                'message' => 'Invalid or expired link'
            ], 403);
        }

        return new AppliedJobResource($appliedJob);
    }
    public function updateByToken(Request $request, $token)
    {
        $appliedJob = AppliedJob::where('access_token', $token)
            ->where('access_token_expires_at', '>', now())
            ->firstOrFail();
        $validated = $request->validate([
            'contact' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'marital_status' => 'nullable|string|max:50',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_number' => 'nullable|string|max:20',
            'parent_name' => 'nullable|string|max:255',
            'parent_relation' => 'nullable|string|max:50',
            'parent_phone_number' => 'nullable|string|max:20',
            'siblings_name' => 'nullable|string|max:255',
            'siblings_relation' => 'nullable|string|max:50',
            'siblings_phone_number' => 'nullable|string|max:20',
            'mother_name' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'experience_years' => 'nullable|numeric|min:0',
            'reference_one_name' => 'nullable|string|max:255',
            'reference_one_number' => 'nullable|string|max:20',
            'reference_one_designation' => 'nullable|string|max:255',
            'reference_one_email' => 'nullable|email|max:255',
            'reference_two_name' => 'nullable|string|max:255',
            'reference_two_number' => 'nullable|string|max:20',
            'reference_two_designation' => 'nullable|string|max:255',
            'reference_two_email' => 'nullable|email|max:255',
        ]);

        if ($request->hasFile('signature')) {
            $validated['signature_path'] = $request->file('signature')->store('signatures', 'public');
            $validated['signature_uploaded'] = true;
        }

        $appliedJob->update($validated);

        return response()->json([
            'message' => 'Updated successfully'
        ]);
    }
}
