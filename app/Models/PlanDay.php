<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanDay extends Model
{
    /* Eloquent relation definitions */
    public function planSteps()     { return $this->hasMany(PlanStep::Class); }
    public function userPlanDays()  { return $this->hasMany(UserPlanDay::Class); }
}
