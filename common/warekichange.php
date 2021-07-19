<?php
function wareki($year)
{
    $eras = [
        ['year' => 2018, 'name' => '令和'],
        ['year' => 1988, 'name' => '平成']
    ];

    foreach ($eras as $era) {
        $base_year = $era['year'];
        $era_name = $era['name'];
        if ($year > $base_year) {
            $era_year = $year - $base_year;
            if ($era_year === 1) {
                return $era_name .'元年';
            }
            return $era_name . $era_year .'年';
        }
    }
    return null;
}
