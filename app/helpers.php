<?php
if (!function_exists('show_hours')) {
    function show_hours($count_hours)
    {
        $count_hours = $count_hours % 100;
        if ($count_hours > 10 && $count_hours < 20)
            return ' часов';
        $count_hours = $count_hours % 10;
        if ($count_hours == 1) {
            return ' час';
        }
        if ($count_hours >= 2 && $count_hours <= 4) {
            return ' часа';
        }
        return ' часов';
    }
}
if (!function_exists('show_days')) {
    function show_days($count_days)
    {
        $count_days = $count_days % 100;
        if ($count_days > 10 && $count_days < 20)
            return ' дней';
        $count_days = $count_days % 10;
        if ($count_days == 1) {
            return ' день';
        }
        if ($count_days >= 2 && $count_days <= 4) {
            return ' дня';
        }
        return ' дней';
    }
}
