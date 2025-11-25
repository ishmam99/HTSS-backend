<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\AttendanceTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{

    public function index(Request $request)
    {
        $attendances = Attendance::with(['times.record.module','user'])
            ->when($request->has('user_id'), function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->has('record_id'), function ($q) use ($request) {
                $q->where('id', $request->record_id);
            })->when($request->has('start_date') && $request->has('end_date'), function ($q) use ($request) {
                $q->whereBetween('date', [$request->start_date, $request->end_date]);
            })->when($request->has('date'), function ($q) use ($request) {
                $q->whereDate('date', $request->date);
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
                'date' => $request->date,
                'user_id' => auth()->id(),
                'status' =>  $request->status ?? 0,
                'total_working_minute' => 0,
            ]);
            $bulkInsertData = [];
            $totalWorkingMinutes = 0;

            foreach ($request->times as $key => $time) {
                $attachmentPath = null;
                    if (isset($request->times[$key]['attachment']) && $request->times[$key]['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                        $attachmentPath = $request->times[$key]['attachment']->store('attendance_attachments', 'public');
                    }
                 $bulkInsertData[] = [
                    'attendance_id' => $attendance->id,
                    'record_id' => $time['record_id'],
                    'type_of_work' => $time['type_of_work'],
                    'notes' => $time['notes'] ?? null,
                    'total_minute' => $time['total_minute'],
                    'status' => $time['status'] ?? 0,
                    'attachment' => $attachmentPath,
                    'created_at' => now(),
                    'updated_at' => now(),
                 ];
                 $totalWorkingMinutes += $time['total_minute'];
            }
            DB::table('attendance_times')->insert($bulkInsertData);
            $attendance->update([
                'total_working_minute' => $totalWorkingMinutes,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Attendance Submitted!',
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(AttendanceRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $attendance = Attendance::findOrFail($id);

            $attendance->update([
                'date' => $request->date,
                'user_id' => $request->user_id,
                'status' => $request->status ?? 1,
            ]);

            $oldTimes = DB::table('attendance_times')->where('attendance_id', $attendance->id)->get();
            foreach ($oldTimes as $oldTime) {
                if ($oldTime->attachment && Storage::disk('public')->exists($oldTime->attachment)) {
                    Storage::disk('public')->delete($oldTime->attachment);
                }
            }
            DB::table('attendance_times')->where('attendance_id', $attendance->id)->delete();

            $bulkInsertData = [];
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

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Attendance Updated successfully!',
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Attendance deleted successfully'
        ],200);
    }

    public function attendanceStatusUpdate(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update([
            'status'=> $request->status
        ]);
         return response()->json([
            'status' => true,
            'message' => 'Attendance status updated successfully'
        ],200);
    }

    public function attendanceTimeStatusUpdate(Request $request, $id)
    {
        $attendance = AttendanceTime::findOrFail($id);
        $attendance->update([
            'status'=> $request->status
        ]);
         return response()->json([
            'status' => true,
            'message' => 'Attendance time status updated successfully'
        ],200);
    }



}
