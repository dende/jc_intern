<?php

namespace App\Http\Controllers;

use App\Semester;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Generate a new Semester completely programmatic.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateNewSemester(Request $request) {
        // Start to add new semester object.
        $newSemester = new Semester();

        // Get start of new Semester by adding one day to end of the last one.
        $lastSemester = Semester::last();
        // Add one day, but make sure it's the start of a month.
        $newSemester->start = (new Carbon($lastSemester->end))->addDay()->startOfMonth();

        // Calculate end of this semester later on.
        $newSemester->end = new Carbon($newSemester->start);

        if (!Config::has('semester')) {
            if ($request->wantsJson()) {
                return \Response::json(['success' => false, 'message' => trans('semester.config_missing')]);
            } else {
                return back()->withErrors(trans('semester.config_missing'));
            }
        }

        // Is this a valid starting month?
        if ($newSemester->start->month == Config::get('semester.summer_term_start')) {
            // Do we have summer term start?
            // Append last two digits of current year to summer term name.
            $newSemester->label = Config::get('semester.summer_term_name') . ' ' . date('y');

            // Calculate ending month of semester
            $newSemester->end->month(Config::get('semester.winter_term_start') - 1);
        } else if ($newSemester->start->month == Config::get('semester.winter_term_start')) {
            // Do we have winter term start?
            // Append last two digits of this and next year to winter term name.
            $newSemester->label = Config::get('semester.winter_term_name') . ' ' . date('y') . '/' . date('y', strtotime('+1 year'));

            // Calculate ending month of semester
            $newSemester->end->month(Config::get('semester.summer_term_start') - 1);
            // And add one to the year for winter term if start of summer term is not first of January.
            if (Config::get('semester.summer_term_start') != '01') {
                $newSemester->end->addYear();
            }
        } else {
            // Something is very wrong. One shouldn't land here.
            if ($request->wantsJson()) {
                return \Response::json(['success' => false, 'message' => trans('semester.wrong_start')]);
            } else {
                return back()->withErrors(trans('semester.wrong_start'));
            }
        }
        // End calculation of end date.
        $newSemester->end = $newSemester->end->endOfMonth();

        if ($newSemester->save()) {
            // Semester successfully saved.
            //TODO: Do we have to trigger sth. here?
            if ($request->wantsJson()) {
                return \Response::json(['success' => true, 'message' => trans('semester.semester_created_successful')]);
            } else {
                $request->session()->flash('message_success', trans('semester.semester_created_successful'));
                return back();
            }
        } else {
            // Semester not successfully saved.
            if ($request->wantsJson()) {
                return \Response::json(['success' => false, 'message' => trans('semester.semester_creation_error')]);
            } else {
                return back()->withErrors(trans('semester.semester_creation_error'));
            }
        }
    }
}
