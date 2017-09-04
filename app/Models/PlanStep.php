<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanStep extends Model
{
    /* Eloquent relation definitions */
    public function userPlanSteps() { return $this->hasMany(UserPlanStep::class); }
    public function planDay()       { return $this->belongsTo(PlanDay::class, 'plan_day_id'); }
    public function chapter()       { return $this->belongsTo(Chapter::class); }
}
