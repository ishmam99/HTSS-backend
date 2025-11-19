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
    $query = EndUser::advancedQuery($request);

    $lists = $request->per_page
        ? $query->paginate($request->per_page)
        : $query->get();

     return response()->json([
            'success' => true,
            'data' => $lists,
           'total' => EndUser::count()
        ]);
}



   public function store(EndUserRequest $request)
    {
         $endUser = null;

        DB::transaction(function () use ($request, &$endUser) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? '12345678'),
                'role' => 'end-user'
            ]);

            $data = $request->validated();
            if(auth()->user()->role == 'customer')
            {
                $data['customer_id'] = auth()->user()->customer->id;
                $data['industry_id'] = auth()->user()->customer->industry_id;
            }
            $data['user_id'] = $user->id;

            // Create EndUser
            $endUser = EndUser::create($data);

            if ($request->has('software_id')) {
            $syncData = [];
            foreach ($request->software_id as $index => $softwareId) {
                $syncData[$softwareId] = [
                    'level' => $request->level[$index] ?? null
                ];
            }
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
        if($request->filled('name'))
        {
        $endUser->user->update([
                        'name' => $request->name,
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
