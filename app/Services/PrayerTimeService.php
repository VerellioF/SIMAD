<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;

class PrayerTimeService
{
    /**
     * Approximate calculation for Blitar, East Java (Kemenag standards)
     * Coordinates: Latitude: -8.0983, Longitude: 112.1681, Timezone: UTC+7
     */
    public function getSchedule(?Carbon $date = null): array
    {
        $date = $date ?? Carbon::now('Asia/Jakarta');
        $dayOfYear = (int) $date->format('z') + 1;

        // Solar Declination & Equation of Time approximation
        $b = 2 * M_PI * ($dayOfYear - 81) / 365;
        $equationOfTime = 9.87 * sin(2 * $b) - 7.53 * cos($b) - 1.5 * sin($b); // in minutes
        $solarDeclination = 23.45 * sin($b); // in degrees

        $lat = -8.0983; // Blitar
        $lng = 112.1681;
        $timezone = 7.0;

        // Solar noon in hours (LMT to Zone Time)
        $noonHours = 12 + ($timezone - ($lng / 15.0)) - ($equationOfTime / 60.0);

        // Subuh: angle 20 deg below horizon
        $subuhHourAngle = $this->calculateHourAngle($lat, $solarDeclination, -20.0);
        // Syuruq (Sunrise): angle -0.833 deg
        $sunriseHourAngle = $this->calculateHourAngle($lat, $solarDeclination, -0.833);
        // Maghrib (Sunset): angle -0.833 deg
        $sunsetHourAngle = $sunriseHourAngle;
        // Isya: angle 18 deg below horizon
        $isyaHourAngle = $this->calculateHourAngle($lat, $solarDeclination, -18.0);

        // Ashar: Shadow length = tan(abs(lat - declination)) + 1 (Shafi'i)
        $declRad = deg2rad($solarDeclination);
        $latRad = deg2rad($lat);
        $asharAngleRad = atan(1 / (1 + tan(abs($latRad - $declRad))));
        $asharAngle = rad2deg($asharAngleRad);
        $asharHourAngle = $this->calculateHourAngle($lat, $solarDeclination, $asharAngle);

        // Calculation in hours with standard 2-minute ihtiyat (precaution)
        $ihtiyatMinutes = 2.0 / 60.0;

        $subuh = $this->formatHour($noonHours - $subuhHourAngle + $ihtiyatMinutes);
        $terbit = $this->formatHour($noonHours - $sunriseHourAngle);
        $dzuhur = $this->formatHour($noonHours + $ihtiyatMinutes);
        $ashar = $this->formatHour($noonHours + $asharHourAngle + $ihtiyatMinutes);
        $maghrib = $this->formatHour($noonHours + $sunsetHourAngle + $ihtiyatMinutes);
        $isya = $this->formatHour($noonHours + $isyaHourAngle + $ihtiyatMinutes);

        $times = [
            'Subuh' => $subuh,
            'Terbit' => $terbit,
            'Dzuhur' => $dzuhur,
            'Ashar' => $ashar,
            'Maghrib' => $maghrib,
            'Isya' => $isya,
        ];

        // Determine next upcoming prayer
        $currentTime = $date->format('H:i');
        $nextPrayer = 'Subuh';

        foreach ($times as $name => $time) {
            if ($name === 'Terbit') {
                continue;
            }
            if ($currentTime < $time) {
                $nextPrayer = $name;
                break;
            }
        }

        return [
            'location' => 'Blitar & Sekitarnya (WIB)',
            'date_formatted' => $date->translatedFormat('l, d F Y'),
            'hijri_formatted' => $this->getHijriDate($date),
            'times' => $times,
            'next_prayer' => $nextPrayer,
            'next_prayer_time' => $times[$nextPrayer] ?? $times['Subuh'],
        ];
    }

    private function calculateHourAngle(float $latitude, float $declination, float $altitude): float
    {
        $latRad = deg2rad($latitude);
        $declRad = deg2rad($declination);
        $altRad = deg2rad($altitude);

        $val = (sin($altRad) - (sin($latRad) * sin($declRad))) / (cos($latRad) * cos($declRad));
        $val = max(-1.0, min(1.0, $val));

        return rad2deg(acos($val)) / 15.0; // returns hours
    }

    private function formatHour(float $hours): string
    {
        $h = (int) floor($hours);
        $m = (int) round(($hours - $h) * 60);

        if ($m >= 60) {
            $h += 1;
            $m -= 60;
        }

        return sprintf('%02d:%02d', $h % 24, $m);
    }

    public function getHijriDate(Carbon $date): string
    {
        // Islamic Hijri calendar offset approximation
        // 2026 is approx 1447 - 1448 H
        $jd = gregoriantojd((int) $date->format('m'), (int) $date->format('d'), (int) $date->format('Y'));
        $l = $jd - 1948440 + 10632;
        $n = (int) floor(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = ((int) floor((10985 - $l) / 5316)) * ((int) floor((50 * $l) / 17719)) + ((int) floor($l / 5670)) * ((int) floor((43 * $l) / 15238));
        $l = $l - ((int) floor((30 - $j) / 15)) * ((int) floor((17719 * $j) / 50)) - ((int) floor($j / 16)) * ((int) floor((15238 * $j) / 43)) + 29;
        $month = (int) floor((24 * $l) / 709);
        $day = $l - (int) floor((709 * $month) / 24);
        $year = 30 * $n + $j - 30;

        $hijriMonths = [
            1 => 'Muharram', 2 => 'Safar', 3 => 'Rabiul Awwal', 4 => 'Rabiul Akhir',
            5 => 'Jumadil Ula', 6 => 'Jumadil Akhir', 7 => 'Rajab', 8 => 'Sya\'ban',
            9 => 'Ramadhan', 10 => 'Syawwal', 11 => 'Dzulqa\'dah', 12 => 'Dzulhijjah',
        ];

        $monthName = $hijriMonths[$month] ?? 'Hijriah';

        return "{$day} {$monthName} {$year} H";
    }
}
