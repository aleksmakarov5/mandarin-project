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
if (!function_exists('str2number')) {
    function str2number($str)
    {
        $str = str_replace(',', '.', preg_replace('/[^,.0-9]/', '', $str));
        if (!$str)
            $str = 0;
        return round((float)($str), 2);
    }
}
if (!function_exists('mb_ucfirst') && extension_loaded('mbstring')) {
    /**
     * mb_ucfirst - преобразует первый символ в верхний регистр
     * @param string $str - строка
     * @param string $encoding - кодировка, по-умолчанию UTF-8
     * @return string
     */
    function mb_ucfirst($str, $encoding = 'UTF-8')
    {
        $str = mb_ereg_replace('^[\ ]+', '', $str);
        $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
            mb_substr($str, 1, mb_strlen($str), $encoding);
        return $str;
    }
}
if (!function_exists('in_money')) {
    function in_money($value, $decimals = 2, $dec_point = ',', $thousands_sep = ' ')
    {
        return number_format((float) $value, $decimals, $dec_point, $thousands_sep);
    }
}
if (!function_exists('dec2')) {
    function dec2($value, $decimals = 2, $dec_point = '.', $thousands_sep = '')
    {
        return number_format((float) $value, $decimals, $dec_point, $thousands_sep);
    }
}
if (!function_exists('bold_text')) {
    function bold_text($text)
    {
        return '</w:t></w:r><w:r><w:rPr><w:b/></w:rPr><w:t xml:space="preserve">' . $text . '</w:t></w:r><w:r><w:t xml:space="preserve">';
    }
}
