<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Jobs\ProcessWondeSync;

class SchoolController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function getSchool()
    {
        return School::firstOrFail();
    }

    public function resyncSchoolData()
    {
        $wondeAccessToken = config('app.wonde_token');
        abort_if(!$wondeAccessToken, 500, 'Wonde Access Token not set');
        ProcessWondeSync::dispatch();
        return true;
    }
}
