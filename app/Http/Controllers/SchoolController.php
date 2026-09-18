<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolApplication;
use App\Models\UserActivity;

class SchoolController extends Controller
{
    /**
     * Display the school registration form.
     */
    public function index()
    {
        return view('school.index');
    }

    /**
     * Store a new school admission application.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'experience_level' => 'required|string|in:beginner,intermediate,advanced',
            'preferred_track' => 'required|string|in:technical_analysis,macro_fundamentals,forex_commodities,crypto_risk',
            'schedule' => 'required|string|in:weekends,weekday_evenings,mentorship',
            'goals' => 'nullable|string|max:2000',
        ]);

        $application = SchoolApplication::create([
            'user_id' => $request->user()?->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'experience_level' => $validated['experience_level'],
            'preferred_track' => $validated['preferred_track'],
            'schedule' => $validated['schedule'],
            'goals' => $validated['goals'] ?? null,
            'status' => 'pending',
        ]);

        if ($request->user()) {
            UserActivity::log(
                $request->user(),
                'school_application',
                'Submitted application for Pipnomics Trading School',
                ['application_id' => $application->id, 'track' => $application->preferred_track],
                $request
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your application has been received! An academy mentor will contact you via email and phone/WhatsApp shortly.',
                'application' => $application,
            ]);
        }

        return redirect()->route('school.index')->with('success', 'Your application has been received! An academy mentor will contact you shortly.');
    }
}
