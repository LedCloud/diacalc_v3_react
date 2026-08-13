<?php

namespace App\Services;

use App\Models\Factor;
use Carbon\Carbon;

class CalculateFactorsService
{
    public static function calculate($factors)
    {
        $now = Carbon::now() ->secondsSinceMidnight() / 60;
        // 1. Подготавливаем коэффициенты: сортируем по времени и добавляем минуты от полуночи
        $factors = $factors->map(function ($factor) {
            // Carbon::parse('08:00:00')->secondsSinceMidnight() / 60
            $factor->minutes = Carbon::parse($factor->time)->secondsSinceMidnight() / 60;
            return $factor;
        })->sortBy('minutes')->values();
        $coefsView = [];
        $totalMinutesInDay = 24 * 60;

        for ($t = 0; $t < 24; $t++) {
            $currentMinutes = $t * 60;

            // --- Поиск BEFORE ---
            // Ищем последний фактор, который меньше или равен текущему времени
            $beforeFactor = $factors->last(fn($f) => $f->minutes <= $currentMinutes) ?? $factors->last();

            $diffBefore = $currentMinutes - $beforeFactor->minutes;
            if ($diffBefore < 0) {
                $diffBefore += $totalMinutesInDay;
            }

            // --- Поиск AFTER ---
            // Ищем первый фактор, который больше или равен текущему времени
            $afterFactor = $factors->first(fn($f) => $f->minutes >= $currentMinutes) ?? $factors->first();

            $diffAfter = $afterFactor->minutes - $currentMinutes;
            if ($diffAfter < 0) {
                $diffAfter += $totalMinutesInDay;
            }

            // --- Расчет пропорции ---
            $length = $diffBefore + $diffAfter;

            if ($length > 0) {
                $k1 = $beforeFactor->k1 - $diffBefore * ($beforeFactor->k1 - $afterFactor->k1) / $length;
                $k2 = $beforeFactor->k2 - $diffBefore * ($beforeFactor->k2 - $afterFactor->k2) / $length;
                $k3 = $beforeFactor->k3 - $diffBefore * ($beforeFactor->k3 - $afterFactor->k3) / $length;
            } else {
                $k1 = $beforeFactor->k1;
                $k2 = $beforeFactor->k2;
                $k3 = $beforeFactor->k3;
            }

            // Форматирование времени строки: '00:00', '01:00' ... '23:00'
            $timeString = sprintf('%02d:00', $t);

            $now_moment = intval($now / 60) == $t;
            $coefsView[$timeString] = [
                'now' => $now_moment,
                'id'   => $t,
                'time' => $timeString,
                'k1'   => number_format($k1, 2),
                'k2'   => number_format($k2, 2),
                'k3'   => number_format($k3, 2),
            ];
        }
        return $coefsView;
//        if ($factors->count() <2) {
//            $one = reset($factors);
//            return $one;
//        }
//
//        $now = now();
//        $after = $before = null;
//        foreach ($factors as $factor) {
//            $modelTime = Carbon::parse($factor->time);
//            if ($modelTime->lte($now)) {
//                $before = $factor->time;
//            }
//
//            }
//        }
//
//        return Factor();
    }

    protected static function findBefore($factors, $time){
        $found = count($factors) - 1;
        for($i=count($factors)-1; $i >= 0; $i--){
            if (static::getTime($factors[$i]['time']) <= $time){
                $found = $i;
                break;
            }
        }
        // для 7 предыдуший 15, разница д.б. 7 + 24 - 15 = 16
        $d = $time - static::getTime($factors[$found]['time']);
        if ($d < 0){
            $d += 24 * 60;
        }
        return array(
            'diff' => $d,
            'time' => $factors[$found]['time'],
            'k1'   => $factors[$found]['k1'],
            'k2'   => $factors[$found]['k2'],
            'k3'   => $factors[$found]['k3']
        );
    }

    protected static function findAfter($factors, $time){ //$cs - массив коэф-ов, $t - час в минутах
        //надо вернуть структуру разница в минутах и три коэф-та
        $found = 0;
        for($i=0; $i < count($factors); $i++){
            if (static::getTime($factors[$i]['time']) >= $time){
                //Нашли, возвращаем найденное
                $found = $i;
                break;
            }
        }
        //Не нашли, Значит надо отдать первый из списка
        //12 - 10 = 2
        //8 - 10 = -2, на самом деле разница - 24-10+8
        $d = static::getTime($factors[$found]['time']) - $time;
        if ($d < 0){
            $d += 24 * 60;
        }

        return array(
            'diff' => $d,
            'time' => $factors[$found]['time'],
            'k1'   => $factors[$found]['k1'],
            'k2'   => $factors[$found]['k2'],
            'k3'   => $factors[$found]['k3']
        );
    }

    /**
     * Time to minutes since midnight
     * @param string $time_string "08:00"
     * @return int minutes since midnight
     */
    protected static function getTime($time_string){
        if (!preg_match('/^(\d{2}):(\d{2})$/', $time_string, $matches)){
            return 0;
        }
        return intval($matches[1]) * 60 + intval($matches[2]);
    }
}
