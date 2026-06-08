<?php

namespace App\Models;

use App\Traits\HasAdvancedQuery;
use Illuminate\Database\Eloquent\Model;

class TrainingCourseSchedule extends Model
{
    //
    use HasAdvancedQuery;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    // In your TrainingCourseSchedule model, add these relationships and methods

public function trainingCourse()
{
    return $this->belongsTo(TrainingCourse::class);
}

public function trainer()
{
    return $this->belongsTo(User::class, 'trainer_id');
}

// public function enrollments()
// {
//     return $this->hasMany(Enrollment::class); // Adjust to your enrollment model
// }

public function isAvailable()
{
    $bookedSeats = 0 ;
    // $bookedSeats = $this->enrollments()->count();
    return $this->status == 1 && 
           $this->date >= now()->toDateString() && 
           ($this->available_seats - $bookedSeats) > 0;
}

public function getAvailableSeatsCountAttribute()
{
    $bookedSeats = 0;
    // $bookedSeats = $this->enrollments()->count();
    return max(0, $this->available_seats - $bookedSeats);
}

public function scopeActive($query)
{
    return $query->where('status', 1)->where('date', '>=', now());
}

public function scopeUpcoming($query)
{
    return $query->where('date', '>=', now()->toDateString());
}

public function scopeByCourse($query, $courseId)
{
    return $query->where('training_course_id', $courseId);
}
}
