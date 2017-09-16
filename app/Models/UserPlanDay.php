<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlanDay extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'plan_day_id', 'status',
    ];

    /* Eloquent relation definitions */
    public function planDay()       { return $this->belongsTo(PlanDay::Class); }
    public function userPlanSteps() { return $this->hasMany(UserPlanStep::Class); }
}
