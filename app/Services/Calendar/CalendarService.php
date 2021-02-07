<?php

namespace App\Services\Calendar;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CalendarService
{
    /**
     * Returns a collection of Calendar Busy times.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public static function getCalendarBusyTimes(Carbon $startDate, Carbon $endDate)
    {
        $timezone = self::getCalendarTimezone();
        $period = CarbonPeriod::since($startDate->startOfHour()->tz($timezone))->hours(1)->until($endDate->tz($timezone));
        $hoursBusy = [8, 9, 12, 14, 16];

        $dates = [];

        foreach ($period as $date) {
            $isBusy = array_search($date->hour, $hoursBusy);

            if ($isBusy > -1) {
                $dates[] = [
                    'start_date' => $date,
                    'end_date' => $date->copy()->addHour(),
                ];
            }
        }

        return $dates;
    }

    /**
     * Returns a collection of Calendar Free times.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public static function getCalendarFreeTimes(Carbon $startDate, Carbon $endDate, $localTimeZone)
    {
        // TODO validate $localTimeZone
        // I imagine there is probably a more performative associative array difference function that I'm missing here, but this works

        // Work Dates (assuming 1 hour intervals)
        $period = CarbonPeriod::since($startDate->startOfHour()->tz($localTimeZone))->hours(1)->until($endDate->tz($localTimeZone));
        $hoursAvailable = [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19];

        $workDates = [];

        foreach ($period as $date) {
            $available = array_search($date->hour, $hoursAvailable);

            if ($available > -1) {
                $workDates[] = [
                    'start_date' => $date,
                    'end_date' => $date->copy()->addHour(),
                ];
            }
        }

        // Convert Busy Times to local TimeZone
        $calendarBusyTimes = self::getCalendarBusyTimes($startDate, $endDate);
        $busyDates = [];
        foreach ($calendarBusyTimes as $busyTime) {
            $busyDates[] = $busyTime['start_date']->tz($localTimeZone);
        }

        // Compare, remove BusyDates
        $freeDates = $workDates;
        foreach ($freeDates as $index => $workHour) {
            if (in_array($workHour['start_date'], $busyDates)) {
                unset($freeDates[$index]);
            }
        }

        return $freeDates;
    }

    /**
     * Returns the Calendar Timezone setting.
     *
     * @return string
     */
    public static function getCalendarTimezone()
    {
        return 'America/Los_Angeles';
    }
}
