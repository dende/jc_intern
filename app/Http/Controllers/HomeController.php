<?php

namespace App\Http\Controllers;

use App\Birthday;
use App\Rehearsal;
use App\Gig;
use App\Semester;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $next_rehearsal = Rehearsal::getNextRehearsal();
        if (null === $next_rehearsal) {
            //TODO: Move to lang.
            $rehearsal = ['diff' => 'nie', 'datetime' => 'keine Probe gefunden', 'location' => 'nirgendwo'];
        } else {
            $rehearsal = ['diff' => $next_rehearsal->start->diffForHumans(), 'datetime' => $next_rehearsal->start->formatLocalized('%c'), 'location' => $next_rehearsal->place];
        }

        $next_gig = Gig::getNextGig();
        if (null === $next_gig) {
            $gig = ['diff' => 'nie', 'datetime' => 'keine Probe gefunden', 'location' => 'nirgendwo'];
        } else {
            $gig = ['diff' => $next_gig->start->diffForHumans(), 'datetime' => $next_gig->start->toDayDateTimeString(), 'location' => $next_gig->place];
        }

        //Consider Birthdays 3 days in the past and 10 days in the future
        $upcoming_birthdays = Birthday::all()->filter(function($value) {
            return $value->getStart()->gte(Carbon::now()->subDays(3)) && $value->getStart()->lte(Carbon::now()->addDays(10));});

        // Show "add semester" if current semester is (almost) over and it's the last one.
        $semester_warning = (new Carbon(Semester::current()->end))->diffInMonths(Carbon::today()) <= 1;

        return view('home', [
            'next_rehearsal' => $rehearsal,
            'next_gig' => $gig,
            'upcoming_birthdays' => $upcoming_birthdays,
            'semester_warning' => $semester_warning
        ]);
    }
}
