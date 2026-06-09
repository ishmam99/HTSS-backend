<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SoftwareRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SoftwareRequestController extends Controller
{
    /**
     * Display a listing of software requests.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = SoftwareRequest::with(['software', 'solution']);
            
            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by is_converted
            if ($request->has('is_converted')) {
                $query->where('is_converted', $request->is_converted);
            }
            
            // Filter by software_id
            if ($request->has('software_id')) {
                $query->where('software_id', $request->software_id);
            }
            
            // Filter by solution_id
            if ($request->has('solution_id')) {
                $query->where('solution_id', $request->solution_id);
            }
            
            // Search by email, name, or company_name
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
            }
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $softwareRequests = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $softwareRequests,
                'message' => 'Software requests retrieved successfully'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve software requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created software request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'record_id' => 'nullable|integer',
                'email' => 'required|email|max:255',
                'name' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'software_id' => 'required|exists:softwares,id',
                'solution_id' => 'required|exists:solutions,id',
                'phone' => 'nullable|string|max:50',
                'billing_street' => 'nullable|string|max:255',
                'billing_city' => 'nullable|string|max:100',
                'billing_country' => 'nullable|string|max:100',
                'billing_state' => 'nullable|string|max:100',
                'account_data' => 'nullable|array',
                'status' => 'nullable|in:0,1,2',
                'is_converted' => 'nullable|in:0,1,2',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $data = $request->all();
            
            // Handle JSON field
            if (isset($data['account_data'])) {
                $data['account_data'] = json_encode($data['account_data']);
            }
            
            $softwareRequest = SoftwareRequest::create($data);
            
            // Load relationships
            $softwareRequest->load(['software', 'solution']);
            
            return response()->json([
                'success' => true,
                'data' => $softwareRequest,
                'message' => 'Software request created successfully'
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create software request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified software request.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $softwareRequest = SoftwareRequest::with(['software', 'solution'])->findOrFail($id);
            
            // Decode JSON field
            if ($softwareRequest->account_data) {
                $softwareRequest->account_data = json_decode($softwareRequest->account_data, true);
            }
            
            return response()->json([
                'success' => true,
                'data' => $softwareRequest,
                'message' => 'Software request retrieved successfully'
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Software request not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve software request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update the specified software request.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $softwareRequest = SoftwareRequest::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'record_id' => 'nullable|integer',
                'email' => 'sometimes|required|email|max:255',
                'name' => 'sometimes|required|string|max:255',
                'company_name' => 'sometimes|required|string|max:255',
                'software_id' => 'sometimes|required|exists:softwares,id',
                'solution_id' => 'sometimes|required|exists:solutions,id',
                'phone' => 'nullable|string|max:50',
                'billing_street' => 'nullable|string|max:255',
                'billing_city' => 'nullable|string|max:100',
                'billing_country' => 'nullable|string|max:100',
                'billing_state' => 'nullable|string|max:100',
                'account_data' => 'nullable|array',
                'status' => 'nullable|in:0,1,2',
                'is_converted' => 'nullable|in:0,1,2',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $data = $request->all();
            
            // Handle JSON field
            if (isset($data['account_data'])) {
                $data['account_data'] = json_encode($data['account_data']);
            }
            
            $softwareRequest->update($data);
            
            // Load relationships
            $softwareRequest->load(['software', 'solution']);
            
            // Decode JSON field for response
            if ($softwareRequest->account_data) {
                $softwareRequest->account_data = json_decode($softwareRequest->account_data, true);
            }
            
            return response()->json([
                'success' => true,
                'data' => $softwareRequest,
                'message' => 'Software request updated successfully'
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Software request not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update software request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified software request.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $softwareRequest = SoftwareRequest::findOrFail($id);
            $softwareRequest->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Software request deleted successfully'
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Software request not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete software request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update the status of a software request.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:0,1,2'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $softwareRequest = SoftwareRequest::findOrFail($id);
            $softwareRequest->status = $request->status;
            $softwareRequest->save();
            
            $statusText = ['pending', 'approved', 'rejected'][$request->status];
            
            return response()->json([
                'success' => true,
                'data' => $softwareRequest,
                'message' => "Software request status updated to {$statusText}"
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Software request not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update the conversion status of a software request.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateConversionStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'is_converted' => 'required|in:0,1,2'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $softwareRequest = SoftwareRequest::findOrFail($id);
            $softwareRequest->is_converted = $request->is_converted;
            $softwareRequest->record_id = $request->record_id ?? $softwareRequest->record_id; // Update record_id if provided
            
            $softwareRequest->save();
            
            $statusText = ['pending', 'converted', 'failed'][$request->is_converted];
            
            return response()->json([
                'success' => true,
                'data' => $softwareRequest,
                'message' => "Software request conversion status updated to {$statusText}"
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Software request not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update conversion status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}