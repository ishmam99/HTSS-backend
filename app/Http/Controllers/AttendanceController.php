<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\AttendanceInfo;
use App\Models\AttendanceTime;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{

    public function index(Request $request)
    {
        $attendances = Attendance::with(['times.record.module', 'user'])
            ->when($request->has('user_id'), function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->has('record_id'), function ($q) use ($request) {
                $q->where('id', $request->record_id);
            })->when($request->has('start_date') && $request->has('end_date'), function ($q) use ($request) {
                $q->whereBetween('date', [$request->start_date, $request->end_date]);
            })->when($request->has('date'), function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            })->when($request->has('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('date', 'desc');
        $lists = $request->per_page
            ? $attendances->paginate($request->per_page)
            : $attendances->get();

        return AttendanceResource::collection($lists);
    }

    public function store(AttendanceRequest $request)
    {
        DB::beginTransaction();
        try {
            $attendance = Attendance::create([
                'date'                 => $request->date,
                'user_id'              => auth()->id(),
                'status'               => $request->status ?? 0,
                'total_working_minute' => 0,
            ]);
            $bulkInsertData      = [];
            $totalWorkingMinutes = 0;

            foreach ($request->times as $key => $time) {
                $attachmentPath = null;
                if (isset($request->times[$key]['attachment']) && $request->times[$key]['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                    $attachmentPath = $request->times[$key]['attachment']->store('attendance_attachments', 'public');
                }
                $bulkInsertData[]  = [
                    'attendance_id' => $attendance->id,
                    'record_id'     => $time['record_id'],
                    'type_of_work'  => $time['type_of_work'],
                    'notes'         => $time['notes'] ?? null,
                    'total_minute'  => $time['total_minute'],
                    'status'        => $time['status'] ?? 0,
                    'attachment'    => $attachmentPath,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
                $totalWorkingMinutes += $time['total_minute'];
            }
            DB::table('attendance_times')->insert($bulkInsertData);
            $attendance->update([
                'total_working_minute' => $totalWorkingMinutes,
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Attendance Submitted!',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function show(Attendance $attendance)
    {
        return AttendanceResource::make($attendance);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $attendance = Attendance::findOrFail($id);

            $attendance->update([
                'date'    => $request->date,
                'user_id' => $request->user_id,
                'status'  => $request->status ?? 1,
            ]);
            if ($request->has('times')) {
                $oldTimes = DB::table('attendance_times')->where('attendance_id', $attendance->id)->get();
                foreach ($oldTimes as $oldTime) {
                    if ($oldTime->attachment && Storage::disk('public')->exists($oldTime->attachment)) {
                        Storage::disk('public')->delete($oldTime->attachment);
                    }
                }
                DB::table('attendance_times')->where('attendance_id', $attendance->id)->delete();

                $bulkInsertData      = [];
                $totalWorkingMinutes = 0;

                foreach ($request->times as $key => $time) {
                    $attachmentPath = null;

                    if (isset($request->times[$key]['attachment']) && $request->times[$key]['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                        $attachmentPath = $request->times[$key]['attachment']->store('attendance_attachments', 'public');
                    }

                    $bulkInsertData[] = [
                        'attendance_id' => $attendance->id,
                        'record_id'     => $time['record_id'],
                        'type_of_work'  => $time['type_of_work'],
                        'notes'         => $time['notes'] ?? null,
                        'total_minute'  => $time['total_minute'],
                        'status'        => $time['status'] ?? 1,
                        'attachment'    => $attachmentPath,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];

                    $totalWorkingMinutes += $time['total_minute'];
                }

                DB::table('attendance_times')->insert($bulkInsertData);

                $attendance->update([
                    'total_working_minute' => $totalWorkingMinutes,
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Attendance Updated successfully!',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Attendance deleted successfully',
        ], 200);
    }

    public function attendanceStatusUpdate(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update([
            'status' => $request->status,
        ]);
        return response()->json([
            'status'  => true,
            'message' => 'Attendance status updated successfully',
        ], 200);
    }

    public function attendanceTimeStatusUpdate(Request $request, $id)
    {
        $attendance = AttendanceTime::findOrFail($id);
        $attendance->update([
            'status' => $request->status,
        ]);
        return response()->json([
            'status'  => true,
            'message' => 'Attendance time status updated successfully',
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_time' => 'required',
            'date'       => 'required|date',
        ]);

        $user = auth()->user();
        $loginAt = Carbon::parse($request->login_time);
        $date = Carbon::parse($request->date);

        return DB::transaction(function () use ($user, $loginAt, $date) {

            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', $date)
                ->first();

            if (!$attendance) {
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date,
                    'total_working_minute' => 0,
                ]);
            }

            // prevent double login
            $open = AttendanceInfo::where('attendance_id', $attendance->id)
                ->whereNull('logout_time')
                ->first();

            if ($open) {
                return response()->json([
                    'message' => 'Already logged in',
                    'attendance_info_id' => $open->id
                ], 422);
            }

            $info = AttendanceInfo::create([
                'attendance_id' => $attendance->id,
                'login_time' => $loginAt->timestamp,
                'status' => 1
            ]);

            return response()->json([
                'message' => 'Login successful',
                'attendance_id' => $attendance->id,
                'attendance_info_id' => $info->id,
                'login_time' => $loginAt->format('Y-m-d H:i:s')
            ]);
        });
    }

    public function logout(Request $request, $id)
    {
        $request->validate([
            'logout_time' => 'required'
        ]);

        $user = auth()->user();
        $logoutAt = Carbon::parse($request->logout_time);

        $attendanceInfo = AttendanceInfo::with('attendance')
            ->where('id', $id)
            ->first();



        if ($attendanceInfo->logout_time) {
            return response()->json([
                'message' => 'Already logged out'
            ], 422);
        }

        $loginAt = Carbon::createFromTimestamp($attendanceInfo->login_time);

        if ($logoutAt->lessThan($loginAt)) {
            $logoutAt->addDay();
        }

        $minutes = $loginAt->diffInMinutes($logoutAt);

        $attendanceInfo->update([
            'logout_time' => $logoutAt->timestamp,
            'status' => 0
        ]);

        $attendance = $attendanceInfo->attendance;
        $attendance->increment('total_working_minute', $minutes);

        return response()->json([
            'message' => 'Logout successful',
            'worked_minutes' => $minutes,
            'total_minutes_today' => $attendance->total_working_minute
        ]);
    }

    public function attendanceTimeStore(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'type_of_work' => 'nullable|string',
            'record_id' => 'nullable|exists:records,id',
            'activity' => 'nullable|string',
            'hour' => 'required|integer',
            'minute' => 'required|integer',
            'status' => 'required',
            'task_name' => 'nullable',
            'description' => 'nullable',
            'output' => 'nullable',
        ]);
        $total_minutes = ($request->hour * 60) + $request->minute;
        AttendanceTime::create([
            'attendance_id' => $request->attendance_id,
            'type_of_work' => $request->type_of_work,
            'record_id' => $request->record_id,
            'activity' => $request->activity,
            'total_minute' => $total_minutes, // Use the calculated total_minutes
            'status' => $request->status,
            'task_name' => $request->task_name,
            'output' => $request->output,
            'description' => $request->description
        ]);
        return response()->json([
            'message' => 'AttendanceTime create successful',
            'total_minutes' => $total_minutes
        ]);
    }
}
