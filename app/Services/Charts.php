<?php

namespace App\Services;

class Charts
{

    private $snelstart;

    public function __construct()
    {
        $this->snelstart = new Snelstart();
    }

    protected static function months()
    {
        return [
            '1' => 'Januari',
            '2' => 'Februari',
            '3' => 'Maart',
            '4' => 'April',
            '5' => 'Mei',
            '6' => 'Juni',
            '7' => 'Juli',
            '8' => 'Augustus',
            '9' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'December'
        ];
    }

    public function getPeriodNumbers($firstYear, $lastYear, $values = [])
    {
        for ($i = $firstYear; $i <= $lastYear; $i++) {
            foreach (self::months() as $month => $value) {
                $startDay = date('Y-m-d', strtotime('01-' . $month . '-' . $i));
                $endDay = date('Y-m-t', strtotime($startDay));

                $grootboeken = $this->snelstart->totalNumbers($startDay, $endDay);
                $values[$i][$month] = $this->snelstart->getRevenue($grootboeken);
            }
        }
        return $values;
    }
}
