<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\CC\ReaderProgress;

class UserLogin
{
    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        ReaderProgress::checkAndUpdate();
    }
}
