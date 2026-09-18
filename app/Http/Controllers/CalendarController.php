<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index()
    {
        // Fetch economic calendar from faireconomy CDN and cache for 2 hours (7200 seconds)
        $events = Cache::remember('economic_calendar_events_v2', 7200, function () {
            try {
                $response = Http::timeout(10)->get('https://nfs.faireconomy.media/ff_calendar_thisweek.json');
                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data) && is_array($data)) {
                        return $data;
                    }
                }
            } catch (\Exception $e) {
                logger()->error('Failed to fetch economic calendar from CDN: ' . $e->getMessage());
            }
            return null;
        });

        // Fallback mock events if the API is down or unreachable
        if (empty($events)) {
            $events = [
                [
                    'title' => 'Core Retail Sales m/m',
                    'country' => 'USD',
                    'date' => now()->format('Y-m-d\TH:i:sP'),
                    'impact' => 'High',
                    'forecast' => '0.2%',
                    'previous' => '0.1%',
                ],
                [
                    'title' => 'CB Consumer Confidence',
                    'country' => 'USD',
                    'date' => now()->addHours(2)->format('Y-m-d\TH:i:sP'),
                    'impact' => 'Medium',
                    'forecast' => '104.0',
                    'previous' => '103.3',
                ],
                [
                    'title' => 'Monetary Policy Meeting Minutes',
                    'country' => 'AUD',
                    'date' => now()->addHours(5)->format('Y-m-d\TH:i:sP'),
                    'impact' => 'Low',
                    'forecast' => '',
                    'previous' => '',
                ]
            ];
        }

        return Inertia::render('Calendar/Index', ['events' => $events]);
    }
}

