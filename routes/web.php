<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Services\Calendar\CalendarService;
use Carbon\Carbon;

Route::get('/', function () {
    $calendarService = new CalendarService;
    $startDate = Carbon::now()->addDay();
    $endDate = Carbon::now()->addWeek();

    $getCalendarBusyTimes = $calendarService->getCalendarBusyTimes($startDate,$endDate);
    dd($getCalendarBusyTimes);
});

Route::get('/test', function () {
    $calendarService = new CalendarService;
    $startDate = Carbon::now()->addDay();
    $endDate = Carbon::now()->addWeek();

    $getCalendarFreeTimes = $calendarService->getCalendarFreeTimes($startDate,$endDate, 'America/New_York');
    dd($getCalendarFreeTimes);
});
