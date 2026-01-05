<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobRequest;
use App\Http\Resources\JobResource;
use App\Models\JobOffer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = JobOffer::advancedQuery($request);
        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        $lists = JobOffer::all();

        return response()->json([
            'success' => true,
            'data' => $lists,
        ]);
    }

    public function publicJob(Request $request)
    {
        $query = JobOffer::advancedQuery($request);
        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();
        $lists = JobOffer::all();

        return response()->json([
            'success' => true,
            'data' => $lists,
        ]);
    }

    public function publicJobShow(JobOffer $jobs_offer)
    {
        return new JobResource($jobs_offer->load('department'));
    }

    public function show(JobOffer $jobs_offer)
    {
        return new JobResource($jobs_offer->load('department'));
    }

    public function store(JobRequest $request)
    {
        $data = $request->validated();
        $data['requirements'] = json_encode($data['requirements']) ?? [];
        $data['key_responsibilities'] = json_encode($data['key_responsibilities']) ?? [];
        $data['required_qualifications'] = json_encode($data['required_qualifications']) ?? [];
        $data['key_skills'] = json_encode($data['key_skills']) ?? [];
        $data['primary_software'] = json_encode($data['primary_software']) ?? [];
        $data['created_by'] = auth()->id();
        $data['deadline'] = Carbon::parse( $data['deadline']);
        $data['published_at'] = now();
        $jobOffer = JobOffer::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Job created successfully',
            'data' => $jobOffer,
        ], 201);
    }

    public function update(JobRequest $request, JobOffer $jobs_offer)
    {

        $data = $request->validated();

        if (isset($data['key_responsibilities'])) {
            $data['key_responsibilities'] = json_encode($data['key_responsibilities']);
        }

        if (isset($data['requirements'])) {
            $data['requirements'] = json_encode($data['requirements']);
        }

        if (isset($data['required_qualifications'])) {
            $data['required_qualifications'] = json_encode($data['required_qualifications']);
        }

        if (isset($data['key_skills'])) {
            $data['key_skills'] = json_encode($data['key_skills']);
        }

        if (isset($data['primary_software'])) {
            $data['primary_software'] = json_encode($data['primary_software']);
        }

        $jobs_offer->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Job updated successfully',
            'data' => $jobs_offer,
        ], 200);
    }

    public function destroy(JobOffer $jobs_offer)
    {
        $jobs_offer->delete();

        return response()->json([
            'status' => true,
            'message' => 'Job deleted successfully',
        ], 200);
    }

    public function changeStatus(Request $request, $id)
    {
        $jobs_offer = JobOffer::find($id);
        $jobs_offer->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Job status updated successfully',
        ], 200);
    }
}
