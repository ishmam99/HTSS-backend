<?php

namespace App\Http\Controllers;

use App\Models\CustomerSoftware;
use App\Models\CustomerSolution;
use App\Models\EndUser;
use App\Models\TrainingCourse;
use App\Models\TrainingEnrollment;
use App\Models\TrainingEvent;
use App\Models\TrainingOffer;
use Illuminate\Http\Request;

class CustomerStatsController extends Controller
{
    public function show($customer_id)
    {
        $softwareCount = CustomerSoftware::where('customer_id', $customer_id)->count();
        $solutionCount = CustomerSolution::where('customer_id', $customer_id)->count();
        $trainingCourseCount = TrainingCourse::where('customer_id', $customer_id)->count();

        $trainingEventCount = TrainingEvent::whereIn(
            'training_course_id',
            TrainingCourse::where('customer_id', $customer_id)->pluck('id')
        )->count();

        $trainingOfferCount = TrainingOffer::whereIn(
            'training_event_id',
            TrainingEvent::whereIn(
                'training_course_id',
                TrainingCourse::where('customer_id', $customer_id)->pluck('id')
            )->pluck('id')
        )->count();

        $endUserIds = EndUser::where('customer_id', $customer_id)->pluck('id');

        $trainingEnrollmentCount = TrainingEnrollment::whereIn('end_user_id', $endUserIds)->count();

        return response()->json([
            'customer_id' => $customer_id,
            'softwares' => $softwareCount,
            'solutions' => $solutionCount,
            'training_courses' => $trainingCourseCount,
            'training_events' => $trainingEventCount,
            'training_offers' => $trainingOfferCount,
            'training_enrollments' => $trainingEnrollmentCount,
        ]);
    }
}
