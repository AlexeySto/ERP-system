<?php

namespace app\helpers;

use Exception;
use Yii;
use DateTime;

class Date
{
    public static function timeDiff($time1, $time2) {
        $match1 = preg_match( '/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $time1, $matches1);
        $match2 = preg_match( '/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $time2, $matches2);
        if (!$match1 || !$match2) return 0;

        $x1 = (int)$matches1[1] * 60 + (int)$matches1[2];
        $x2 = (int)$matches2[1] * 60 + (int)$matches2[2];
        return $x2 - $x1;
    }

    public static function monthFormat($monthSql)
    {
        return static::monthName(substr($monthSql, 5,2)) . ' ' . substr($monthSql, 0, 4);
    }

    public static function monthsDaysDiffNow($date)
    {
        $diff  = (new \DateTime())->diff(new \DateTime($date));
        $y = (int)$diff->y;
        $m = (int)$diff->m;
        $d = (int)$diff->d;
        $str = [];
        if ($y > 0) $str[] = static::yearsForm($y);
        if ($m > 0) $str[] = static::monthsForm($m);
        if ($d > 0) $str[] = static::daysForm($d);
        return implode(' ', $str);
    }
    public static function daysForm($days)
    {
        $days = (int)$days;
        $lastNum = (int)substr((string)($days), -1);
        if ($days < 11 || $days > 19) {
            if ($lastNum == 1) return $days . ' день';
            else if ($lastNum >= 2 && $lastNum <= 4) return $days . ' дня';
        }
        return $days . ' дней';
    }

    public static function monthsForm($months)
    {
        $months = (int)$months;
        $lastNum = (int)substr((string)($months), -1);
        if ($months < 11 || $months > 19) {
            if ($lastNum == 1) return $months . ' месяц';
            else if ($lastNum >= 2 && $lastNum <= 4) return $months . ' месяца';
        }
        return $months . ' месяцев';
    }

    public static function yearsForm($years)
    {
        $years = (int)$years;
        $lastNum = (int)substr((string)($years), -1);
        if ($years < 11 || $years > 19) {
            if ($lastNum == 1) return $years . ' год';
            else if ($lastNum >= 2 && $lastNum <= 4) return $years . ' года';
        }
        return $years . ' лет';
    }


    public static function weekdayName($weekDay, $mondayFirst = false)
    {
        $wdays = $mondayFirst ? ['', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'] : ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
        return $wdays[$weekDay] ?? $weekDay;
    }

    public static function monthName($monthNum)
    {
        $monthNum = (int)$monthNum;
        static $names = [
            'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь',
        ];
        return $names[$monthNum - 1] ?? $monthNum;
    }

    public static function monthNameR($monthNum)
    {
        $monthNum = (int)$monthNum;
        static $names = [
            'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня',
            'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря',
        ];
        return $names[$monthNum - 1] ?? $monthNum;
    }

    public static function totalMinutes($date1, $date2)
    {
        if (!($date1 instanceof DateTime)) {
            $date1 = new DateTime($date1);
        }
        if (!($date2 instanceof DateTime)) {
            $date2 = new DateTime($date2);
        }
        return (int)(abs($date1->getTimestamp() - $date2->getTimestamp()) / 60);
    }

    public static function formatMinutes($min = 0)
    {
        if ($min < 60) return $min . ' мин.';
        $h = floor($min / 60);
        $min = $min % 60;
        return $h . ' ч.' . ($min > 0 ? ' ' . $min . ' мин.' : '');
    }


    public static function formatDate($date) {
        return Yii::$app->formatter->format($date, ['date', 'php: d F Y']) ;
    }

    public static function formatDateTime($date, $pdf = false) {
        if ($pdf) {
            return str_replace(' ', '&nbsp;', Yii::$app->formatter->format($date, ['date', 'php:d M Y'])) .
                Yii::$app->formatter->format($date, ['date', 'php: H:i']);
        }
        return Yii::$app->formatter->format($date, ['date', 'php: H:i, d M Y']);
    }
    public static function formatTime($date) {
        return Yii::$app->formatter->format($date, ['date', 'php: H:i']);
    }
    public static function formatCheckpoint($inDateTime, $pdf = false) {
        static $lastDate = '';
        $date = static::formatDate($inDateTime);
        if (!$lastDate || $lastDate != $date) {
            $lastDate = $date;
            return static::formatDateTime($inDateTime, $pdf);
        }
        return static::formatTime($inDateTime);
    }

    public static function convertDateRuToIso($date)
    {
        if (empty($date)) {
            return '';
        }

        $matches = [];

        preg_match("/(\d{2})\.(\d{2})\.(\d{4})/", $date, $matches);

        if (!empty($matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }

        return '';
    }

    public static function convertDatetimeRuToIso($date)
    {
        if (empty($date)) {
            return '';
        }

        $parts = explode(' ', $date);

        if (empty($parts) || count($parts) < 2) {
            return self::convertDateRuToIso($date);
        }

        $resultDate = self::convertDateRuToIso($parts[0]);

        return !empty($resultDate) ? str_replace($parts[0], $resultDate, $date) : '';
    }

    public static function parseDateRange($dateRange)
    {
        $dateRange = str_replace(' ', '', $dateRange);

        if (empty($dateRange) || empty($dates = explode('-', $dateRange))) {
            return [];
        }

        return [
            'from' => self::convertDateRuToIso($dates[0]) . ' 00:00:00',
            'to' => self::convertDateRuToIso($dates[1]) . ' 23:59:59',
        ];
    }

    public static function getTimeDiff($date1, $date2)
    {
        try {
            $datetime1 = new DateTime($date1);
            $datetime2 = new DateTime($date2);

            $interval = $datetime1->diff($datetime2);

        } catch (Exception $e) {
            return [];
        }

        return [
            'days' => $interval->days,
            'hours' => $interval->h,
            'minutes' => $interval->i,
        ];
    }

    public static function getWorkingDaysUsingDateTime(string $monthYear): int
    {
        $start = new DateTime("01-$monthYear");
        $end = clone $start;
        $end->modify('last day of this month');

        $workingDays = 0;

        while ($start <= $end) {
            if ($start->format('N') <= 5) {
                $workingDays++;
            }
            $start->modify('+1 day');
        }

        return $workingDays;
    }
}