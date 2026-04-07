<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserResume extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'resume' => 'array',
    ];

    public function endUser()
    {
        return $this->belongsTo(EndUser::class, 'end_user_id');
    }
}
