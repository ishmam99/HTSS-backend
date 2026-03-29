<?php

namespace App\Http\Controllers;

use App\Models\EndUser;
use App\Http\Requests\EndUserRequest;
use App\Http\Resources\EndUserResource;
use App\Imports\EndUserExcelImport;
use App\Models\EndUserSoftware;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Mail\DynamicMail;
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
            'name' => $request->first_name.' '.$request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password ?? '12345678'),
            'role' => 'end-user'
        ]);

        $data = $request->validated();

        if(auth()->user()->role == 'customer'){
            $data['customer_id'] = auth()->user()->customer->id;
            $data['industry_id'] = auth()->user()->customer->industry_id;
        }

        $data['user_id'] = $user->id;

        $endUser = EndUser::create($data);

        /*
        | Software Sync
        */
        if ($request->software_id) {

            $syncData = [];

            foreach ($request->software_id as $index => $softwareId) {

                $syncData[$softwareId] = [
                    'level' => $request->level[$index] ?? null
                ];
            }

            $endUser->softwares()->sync($syncData);
        }

        /*
        | Solutions Sync
        */
        if ($request->solution_id) {

            $endUser->solutions()->sync($request->solution_id);
        }

        /*
        | Image Upload
        */
        if ($request->hasFile('image')) {

            $path = $request->file('image')->store('uploads/endUser', 'public');

            $endUser->update([
                'image' => $path
            ]);
        }

    });

    return response()->json([
        'status' => true,
        'message' => 'EndUser created successfully',
        'data' => $endUser
    ], 201);
}

    public function show(EndUser $endUser)
    {
        return new EndUserResource($endUser);
    }
    public function getUserByUserId($id)
    {
        $endUser = EndUser::where('user_id',$id)->with('user','customer','industry','softwares','solutions','softwareLevels','trainingEnrollment')->first();
        if($endUser)
            {
                return response()->json($endUser);
                }
                else
                    {
                        return response()->json(['message'=>'User data not found'],404);
                    }
    }
    public function update(EndUserRequest $request, EndUser $endUser)
{
    $data = $request->validated();

    DB::transaction(function () use ($request, $endUser, $data) {

        if ($request->filled('password')) {
            $endUser->user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        if ($request->filled('name')) {
            $endUser->user->update([
                'name' => $request->name
            ]);
        }

        /*
        | Software Sync
        */
        if ($request->software_id) {

            $syncData = [];

            foreach ($request->software_id as $index => $softwareId) {

                $syncData[$softwareId] = [
                    'level' => $request->level[$index] ?? null
                ];
            }

            $endUser->softwares()->sync($syncData);
        }

        /*
        | Solutions Sync
        */
        if ($request->solution_id) {

            $endUser->solutions()->sync($request->solution_id);
        }

        /*
        | Image Update
        */
        if ($request->hasFile('image')) {

            if ($endUser->image && Storage::disk('public')->exists($endUser->image)) {
                Storage::disk('public')->delete($endUser->image);
            }

            $path = $request->file('image')->store('uploads/endUser', 'public');

            $data['image'] = $path;
        }

        $endUser->update($data);
    });

    return response()->json([
        'status' => true,
        'message' => 'EndUser updated successfully',
        'data' => $endUser
    ]);
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
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        // Excel::import(new EndUserExcelImport, $request->file('file'));
        Excel::queueImport(new EndUserExcelImport, $request->file('file'));
       return response()->json([
                'success' => true,
                'message' => 'Import started. It will process in background.'
            ]);
    }

    public function emailSend(Request $request)
        {
            $request->validate([
                'from' => 'required|email',
                'to' => 'required|email',
                'subject' => 'required|string',
                'body' => 'required|string',
            ]);
            $fromEmail = 'test@hitechsoftsys.net'; // real Yahoo BizMail account
$fromName  = 'Hi-Tech Softsys';
$appPassword = 'ufxxkjnllauezyba'; // correct App Password
                Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.bizmail.yahoo.com');
        Config::set('mail.mailers.smtp.port', 587);
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.mailers.smtp.username', $fromEmail);
        Config::set('mail.mailers.smtp.password', $appPassword);

        Config::set('mail.from.address', $fromEmail);
        Config::set('mail.from.name', $fromName);


        try {
  $ffd =  Mail::raw($request->body, function ($message) use ($request, $fromEmail, $fromName) {
        $message->to($request->to)
                ->subject($request->subject)
                ->from($fromEmail, $fromName);
    });
        dd($ffd);
    return response()->json(['message' => 'Email sent successfully']);
} catch (\Exception $e) {
    return response()->json(['error' => $e->getMessage()], 500);
}
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', 'smtp.bizmail.yahoo.com');

            Config::set('mail.mailers.smtp.port', 587);
Config::set('mail.mailers.smtp.encryption', 'tls');

            // Config::set('mail.mailers.smtp.port', 465);
            // Config::set('mail.mailers.smtp.encryption','ssl');
            Config::set('mail.mailers.smtp.username', $request->from);
            Config::set('mail.mailers.smtp.password', 'ufxxkjnllauezyba');

            Config::set('mail.from.address', $request->from);
            Config::set('mail.from.name',$request->from);
                    try {
                 $dsd =   Mail::raw($request->body, function ($message) use ($request) {
                        $message->to($request->to)
                                ->subject($request->subject)
                                ->from($request->from, $request->from);
                    });
                    dd($dsd);
                    return response()->json(['message' => 'Email sent successfully']);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            // Mail::to($request->to)->send(new DynamicMail($request->all()));
            try {
             $d =   Mail::to($request->to)->send(new DynamicMail($request->all()));
             dd($d);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage()
                ], 500);
            }

            return response()->json([
                'message' => 'Email sent successfully'
            ]);
        }
}
