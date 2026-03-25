<?php

namespace App\Http\Controllers;

use App\Models\ProfessionalReference;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfessionalReferenceResource;
use Illuminate\Support\Facades\Storage;

class ProfessionalReferenceController extends Controller
{

    public function index()
    {
        $data = ProfessionalReference::with('user')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return ProfessionalReferenceResource::collection($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string',
            'designation'  => 'nullable|string',
            'company_name' => 'nullable|string',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string',
            'note'         => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data['user_id'] = auth()->id();

        $reference = ProfessionalReference::create($data);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/ProfessionalReference', 'public');
            $reference->update(['image' => $path]);
        }

        return new ProfessionalReferenceResource($reference);
    }

    public function show($id)
    {
        $reference = ProfessionalReference::with('user')->findOrFail($id);

        return new ProfessionalReferenceResource($reference);
    }

    public function update(Request $request, $id)
    {
        $reference = ProfessionalReference::findOrFail($id);

        $data = $request->validate([
            'name'         => 'sometimes|required|string',
            'designation'  => 'nullable|string',
            'company_name' => 'nullable|string',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string',
            'note'         => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {

            if ($reference->image && Storage::disk('public')->exists($reference->image)) {
                Storage::disk('public')->delete($reference->image);
            }


            $path = $request->file('image')->store('uploads/ProfessionalReference', 'public');
            $data['image'] = $path;
        }

        $reference->update($data);

        return new ProfessionalReferenceResource($reference);
    }

    public function destroy($id)
    {
        $reference = ProfessionalReference::findOrFail($id);

        if ($reference->image && Storage::disk('public')->exists($reference->image)) {
            Storage::disk('public')->delete($reference->image);
        }

        $reference->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ]);
    }
}
