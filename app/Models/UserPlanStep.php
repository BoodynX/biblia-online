<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlanStep extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_plan_day_id', 'plan_step_id', 'status',
    ];
    /* Eloquent relation definitions */
    public function userPlanDay() { return $this->belongsTo(UserPlanDay::Class); }
    public function planStep()    { return $this->belongsTo(PlanStep::Class); }
}
