<?php

namespace App\Http\Controllers;

use App\Models\EndUser;
use App\Http\Requests\EndUserRequest;
use App\Http\Resources\EndUserResource;
use App\Models\EndUserSoftware;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class EndUserController extends Controller
{
  public function index(Request $request)
{
    $query = EndUser::query()
        ->when($request->status, function ($query, $status) {
            return $query->where('status', $status);
        })->when($request->software_id, function ($query, $software_id) {
            return $query->whereHas('softwares', function ($q) use ($software_id) {
                $q->where('software_id', $software_id);
            });
        })->orderByDesc('id');

    $lists = $request->per_page
        ? $query->paginate($request->per_page)
        : $query->get();

    return EndUserResource::collection($lists);
}



   public function store(EndUserRequest $request)
    {
         $endUser = null;

        DB::transaction(function () use ($request, &$endUser) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? '12345678'),
            ]);

            $data = $request->validated();
            $data['user_id'] = $user->id;

            // Create EndUser
            $endUser = EndUser::create($data);

            if ($request->has('software_id')) {
            $syncData = [['level' =>$request->level,'software_id'=>$request->software->id]];

            // foreach ($request->software_id as $index => $softwareId) {
            //     $syncData[$softwareId] = [
            //         'level' => $request->level[$index] ?? null
            //     ];
            // }
            $endUser->softwares()->sync($syncData);
        }



            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('uploads/endUser', 'public');
                $endUser->update(['image' => $path]);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'EndUser created successfully',
            'data' => $endUser,
        ], 201);
    }

    public function show(EndUser $endUser)
    {
        return new EndUserResource($endUser);
    }

    public function update(EndUserRequest $request, EndUser $endUser)
    {
        $data = $request->validated();
        if ($request->filled('password')) {
            $endUser->user->update([
                'password' => bcrypt($request->password),
            ]);
        }
        if ($request->hasFile('image')) {
            if ($endUser->image && Storage::disk('public')->exists($endUser->image)) {
                Storage::disk('public')->delete($endUser->image);
            }
            $path = $request->file('image')->store('uploads/endUser', 'public');
            $data['image'] = $path;
        }
        $endUser->update($data);
        return response()->json([
            'status' => true,
            'message' => 'EndUser updated successfully',
            'data' => $endUser
        ], 200);
    }

    public function destroy(EndUser $endUser)
    {
        if ($endUser->image && Storage::disk('public')->exists($endUser->image)) {
            Storage::disk('public')->delete($endUser->image);
        }
        User::where('id', $endUser->user_id)->delete();
        $endUser->delete();
        return response()->json(['status' => true,'message' => 'EndUser deleted successfully'],200);
    }
}
