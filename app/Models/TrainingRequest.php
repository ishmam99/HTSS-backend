<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'training_requests';

    protected $guarded = ['id'];

    protected $casts = [
        'preferred_start_date' => 'date',
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime',
        'reviewed_at' => 'datetime',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'course_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'number_of_participants' => 'integer',
        'rating' => 'integer',
        'certificate_issued' => 'boolean',
    ];

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REJECTED = 'rejected';

    // Training Type Constants
    const TYPE_ONSITE = 'onsite';
    const TYPE_ONLINE = 'online';
    const TYPE_WEBINAR = 'webinar';

    // Experience Level Constants
    const LEVEL_BEGINNER = 'beginner';
    const LEVEL_INTERMEDIATE = 'intermediate';
    const LEVEL_ADVANCED = 'advanced';

    // Payment Status Constants
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_WAIVED = 'waived';
    const PAYMENT_NOT_REQUIRED = 'not_required';

    // Relationships
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTrainingType($query, $type)
    {
        return $query->where('training_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Accessors
    public function getFullCourseNameAttribute()
    {
        return "{$this->course_name} ({$this->course_code})";
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_UNDER_REVIEW => 'bg-blue-100 text-blue-800',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_SCHEDULED => 'bg-purple-100 text-purple-800',
            self::STATUS_COMPLETED => 'bg-gray-100 text-gray-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusTextAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    // Helper Methods
    public function markAsReviewed($userId = null)
    {
        $this->update([
            'status' => self::STATUS_UNDER_REVIEW,
            'reviewed_at' => now(),
            'reviewed_by' => $userId,
        ]);
    }

    public function approve()
    {
        $this->update(['status' => self::STATUS_APPROVED]);
    }

    public function schedule($date, $time, $meetingLink = null, $location = null)
    {
        $this->update([
            'status' => self::STATUS_SCHEDULED,
            'scheduled_date' => $date,
            'scheduled_time' => $time,
            'meeting_link' => $meetingLink,
            'location' => $location,
        ]);
    }

    public function complete($feedback = null, $rating = null)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'feedback' => $feedback,
            'rating' => $rating,
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'admin_notes' => $reason ? ($this->admin_notes ? $this->admin_notes . "\n" : '') . "Cancelled: " . $reason : $this->admin_notes,
        ]);
    }

    public function issueCertificate($certificateUrl)
    {
        $this->update([
            'certificate_issued' => true,
            'certificate_url' => $certificateUrl,
        ]);
    }

    public function recordPayment($amount, $reference = null)
    {
        $this->update([
            'payment_status' => self::PAYMENT_PAID,
            'amount_paid' => $amount,
            'payment_reference' => $reference,
            'paid_at' => now(),
        ]);
    }
    public function trainingCourseSchedule()
    {
        return $this->belongsTo(TrainingCourseSchedule::class, 'training_course_schedule_id');
    }
    public function trainingCourse()
    {
        return $this->belongsTo(TrainingCourse::class, 'course_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
   
    public function trainingEnrollment()
    {
        return $this->hasOne(TrainingEnrollment::class, 'training_request_id');
    }
}