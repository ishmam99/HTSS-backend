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
        $appliedJobs = AppliedJob::with(['job', 'software', 'industry']);

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
        $appliedJob = AppliedJob::with(['job', 'software', 'industry'])->findOrFail($id);
        return new AppliedJobResource($appliedJob);
    }

    /**
     * STORE (Public Applicant)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'                  => 'nullable|string|max:255',
            'email'                      => 'nullable|email|max:255',
            'contact'                    => 'nullable|string|max:20',
            'emergency_contact'          => 'nullable|string|max:20',

            'highest_education'          => 'nullable|string|max:255',
            'university'                 => 'nullable|string|max:255',

            'resume'                     => 'nullable|file|mimes:pdf|max:10240',
            'link'                       => 'nullable|string|max:255',

            // References
            'reference_one_name'         => 'nullable|string|max:255',
            'reference_one_number'       => 'nullable|string|max:20',
            'reference_one_designation'  => 'nullable|string|max:255',
            'reference_one_email'        => 'nullable|email|max:255',

            'reference_two_name'         => 'nullable|string|max:255',
            'reference_two_number'       => 'nullable|string|max:20',
            'reference_two_designation'  => 'nullable|string|max:255',
            'reference_two_email'        => 'nullable|email|max:255',

            // Signature
            'signature'                  => 'nullable|file|mimes:png,jpg,jpeg|max:5120',

            // Relations
            'job_id'                     => 'nullable|exists:job_offers,id',
            'software_id'                => 'nullable|exists:softwares,id',
            'industry_id'                => 'nullable|exists:industries,id',
        ]);

        if ($request->hasFile('resume')) {
            $validated['resume'] = $request->file('resume')->store('resume', 'public');
        }

        if ($request->hasFile('signature')) {
            $validated['signature_path']    = $request->file('signature')->store('signatures', 'public');
            $validated['signature_uploaded'] = true;
        }

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

        $user  = auth()->user() ?? (object)['role' => null];
        $isHR  = $user && in_array($user->role, ['hr-director', 'hr-manager', 'hr-executive', 'hr-vp']);

        if ($isHR) {
            $validated = $request->validate([
                // Scoring
                'technical_skills'    => 'nullable|integer|min:1|max:10',
                'communication'       => 'nullable|integer|min:1|max:10',
                'cultural_fit'        => 'nullable|integer|min:1|max:10',
                'problem_solving'     => 'nullable|integer|min:1|max:10',

                'overall_comment'     => 'nullable|string',
                'recommendation'      => 'nullable|in:hire,no_hire,hold',

                'status'              => 'nullable|integer',

                // Verification flags
                'reference_checked'   => 'nullable',
                'background_verified' => 'nullable',
                'documents_verified'  => 'nullable',

                // Salary
                'expected_salary'     => 'nullable|numeric|min:0',
                'negotiated_salary'   => 'nullable|numeric|min:0',

                // Offer letter fields
                'responsibilities'    => 'nullable|string',
                'benefits'            => 'nullable|string',
                'employment_terms'    => 'nullable|string',
                'terms_clauses'       => 'nullable|string',
                'joining_date'        => 'nullable|date',
                'offering_date'       => 'nullable|date',
                'completed_at'        => 'nullable|date',

                // Background Checks
                'educational_background_check'      => 'nullable',
                'professional_background_check'     => 'nullable',
                'experience_background_check'     => 'nullable',
                'educational_background_check_document' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
                'experience_background_check_document'  => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
                'police_background_check_document'      => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',

                // Relations
                'software_id'         => 'nullable|exists:softwares,id',
                'industry_id'         => 'nullable|exists:industries,id',
            ]);
            if ($request->hasFile('resume')) {
                $validated['resume'] = $request->file('resume')->store('resume', 'public');
            }

            if ($request->hasFile('signature')) {
                $validated['signature_path']    = $request->file('signature')->store('signatures', 'public');
                $validated['signature_uploaded'] = true;
            }

            if ($request->hasFile('educational_background_check_document')) {
                $validated['educational_background_check_document'] = $request->file('educational_background_check_document')->store('background-checks', 'public');
            }

            if ($request->hasFile('experience_background_check_document')) {
                $validated['experience_background_check_document'] = $request->file('experience_background_check_document')->store('background-checks', 'public');
            }

            if ($request->hasFile('police_background_check_document')) {
                $validated['police_background_check_document'] = $request->file('police_background_check_document')->store('background-checks', 'public');
            }

            $appliedJob->update($request->all() + $validated);
        } else {
            $validated = $request->validate([
                'contact'                    => 'nullable|string|max:20',
                'address'                    => 'nullable|string',

                'reference_one_name'         => 'nullable|string|max:255',
                'reference_one_number'       => 'nullable|string|max:20',
                'reference_one_designation'  => 'nullable|string|max:255',
                'reference_one_email'        => 'nullable|email|max:255',

                'reference_two_name'         => 'nullable|string|max:255',
                'reference_two_number'       => 'nullable|string|max:20',
                'reference_two_designation'  => 'nullable|string|max:255',
                'reference_two_email'        => 'nullable|email|max:255',

                // Background Checks
                'educational_background_check'      => 'nullable|boolean',
                'experience_background_check'       => 'nullable|boolean',
                'professional_background_check'     => 'nullable|boolean',
                'educational_background_check_document' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
                'experience_background_check_document'  => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
                'police_background_check_document'      => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
            ]);
            if ($request->hasFile('resume')) {
                $validated['resume'] = $request->file('resume')->store('resume', 'public');
            }

            if ($request->hasFile('signature')) {
                $validated['signature_path']    = $request->file('signature')->store('signatures', 'public');
                $validated['signature_uploaded'] = true;
            }

            if ($request->hasFile('educational_background_check_document')) {
                $validated['educational_background_check_document'] = $request->file('educational_background_check_document')->store('background-checks', 'public');
            }

            if ($request->hasFile('experience_background_check_document')) {
                $validated['experience_background_check_document'] = $request->file('experience_background_check_document')->store('background-checks', 'public');
            }

            if ($request->hasFile('police_background_check_document')) {
                $validated['police_background_check_document'] = $request->file('police_background_check_document')->store('background-checks', 'public');
            }

            $appliedJob->update($validated);
        }

        return new AppliedJobResource($appliedJob->fresh(['job', 'software', 'industry']));
    }

    /**
     * Generate a time-limited access link for the applicant
     */
    public function generateAccessLink($id)
    {
        $appliedJob = AppliedJob::findOrFail($id);

        $token = Str::random(64);

        $appliedJob->update([
            'access_token'            => $token,
            'access_token_expires_at' => Carbon::now()->addDays(3),
        ]);

        return response()->json([
            'link'       => url("/applicant-access/{$token}"),
            'expires_at' => $appliedJob->access_token_expires_at,
        ]);
    }

    /**
     * Delete
     */
    public function destroy($id)
    {
        AppliedJob::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    /**
     * HR Status Change Only
     */
    public function statusChange(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer',
        ]);

        AppliedJob::findOrFail($id)->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated successfully']);
    }

    /**
     * Public token-based read
     */
    public function accessByToken($token)
    {
        $appliedJob = AppliedJob::where('access_token', $token)
            ->with(['job', 'software', 'industry'])
            ->where('access_token_expires_at', '>', now())
            ->first();

        if (!$appliedJob) {
            return response()->json(['message' => 'Invalid or expired link'], 403);
        }

        return new AppliedJobResource($appliedJob);
    }

    /**
     * Applicant self-update via token
     */
    public function updateByToken(Request $request, $token)
    {
        $appliedJob = AppliedJob::where('access_token', $token)
            ->where('access_token_expires_at', '>', now())
            ->firstOrFail();

        $validated = $request->validate([
            'contact'                    => 'nullable|string|max:20',
            'address'                    => 'nullable|string',
            'marital_status'             => 'nullable|string|max:50',
            'spouse_name'                => 'nullable|string|max:255',
            'spouse_number'              => 'nullable|string|max:20',
            'parent_name'                => 'nullable|string|max:255',
            'parent_relation'            => 'nullable|string|max:50',
            'parent_phone_number'        => 'nullable|string|max:20',
            'siblings_name'              => 'nullable|string|max:255',
            'siblings_relation'          => 'nullable|string|max:50',
            'siblings_phone_number'      => 'nullable|string|max:20',
            'mother_name'                => 'nullable|string|max:255',
            'father_name'                => 'nullable|string|max:255',
            'company_name'               => 'nullable|string|max:255',
            'company_phone'              => 'nullable|string|max:20',
            'company_email'              => 'nullable|email|max:255',
            'experience_years'           => 'nullable|integer|min:0',
            'reference_one_name'         => 'nullable|string|max:255',
            'reference_one_number'       => 'nullable|string|max:20',
            'reference_one_designation'  => 'nullable|string|max:255',
            'reference_one_email'        => 'nullable|email|max:255',
            'reference_two_name'         => 'nullable|string|max:255',
            'reference_two_number'       => 'nullable|string|max:20',
            'reference_two_designation'  => 'nullable|string|max:255',
            'reference_two_email'        => 'nullable|email|max:255',
            // Background Checks
          
            'educational_background_check_document' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
            'experience_background_check_document'  => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
            'police_background_check_document'      => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:10240',
        ]);

        if ($request->hasFile('signature')) {
            $validated['signature_path']    = $request->file('signature')->store('signatures', 'public');
            $validated['signature_uploaded'] = true;
        }

        if ($request->hasFile('educational_background_check_document')) {
            $validated['educational_background_check_document'] = $request->file('educational_background_check_document')->store('background-checks', 'public');
        }

        if ($request->hasFile('experience_background_check_document')) {
            $validated['experience_background_check_document'] = $request->file('experience_background_check_document')->store('background-checks', 'public');
        }

        if ($request->hasFile('police_background_check_document')) {
            $validated['police_background_check_document'] = $request->file('police_background_check_document')->store('background-checks', 'public');
        }

        $appliedJob->update($validated);

        return response()->json(['message' => 'Updated successfully']);
    }
}
