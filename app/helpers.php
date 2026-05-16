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
if (!function_exists('make_date')) {
    function make_date($date)
    {
        $findG = mb_strpos($date, 'г');
        if ($findG) {
            $date = mb_substr($date, 0, $findG);
        }
        $date = str_replace(' января ', '.01.', $date);
        $date = str_replace(' февраля ', '.02.', $date);
        $date = str_replace(' марта ', '.03.', $date);
        $date = str_replace(' апреля ', '.04.', $date);
        $date = str_replace(' мая ', '.05.', $date);
        $date = str_replace(' июня ', '.06.', $date);
        $date = str_replace(' июля ', '.07.', $date);
        $date = str_replace(' августа ', '.08.', $date);
        $date = str_replace(' сентября ', '.09.', $date);
        $date = str_replace(' октября ', '.10.', $date);
        $date = str_replace(' ноября ', '.11.', $date);
        $date = str_replace(' декабря ', '.12.', $date);
        $date = str_replace('г.', '', $date);
        // $date = date('d.m.Y', strtotime($date));
        return $date;
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
if (!function_exists('clearHTML')) {
    function clearHTML($val)
    {
        $val = strip_tags($val);
        $val = preg_replace('/<[^>]*>?/', '', $val);
        $val = preg_replace('/&nbsp;/', '', $val);
        $val = preg_replace('/[\x00-\x1F\x7F]/u', '', $val);
        $val = str_replace('nbsp;', '', $val);
        $val = str_replace('amp;', '', $val);
        $val = str_replace('amp;', '', $val);
        $val = str_replace('lt;', '', $val);
        $val = str_replace('gt;', '', $val);
        $val = str_replace('&#39;', "'", $val);
        $val = str_replace('&#34;', '"', $val);
        $val = str_replace('br', "", $val);
        $val = str_replace('&', "", $val);
        $val = str_replace('>', "", $val);
        $val = str_replace('<', "менее ", $val);
        $val = trim($val);

        return $val;
    }
}

if (!function_exists('notCounter')) {
    function notCounter($applications, $user_id)
    {
        $count = ['application' => 0, 'chat' => 0, 'message' => 0];
        foreach ($applications as $application) {
            $readed = App\Models\status_application::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['application']++;
            $readed = App\Models\status_chat::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['chat']++;
            $readed = App\Models\status_message::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['message']++;
        }
        return $count;
    }
}

if (!function_exists('appNotification')) {
    function appNotification($applicationId, $userId)
    {
        $count = ['application' => 0, 'chat' => 0, 'message' => 0];
        $readed = App\Models\status_application::where('id_user', '=', $userId)
            ->where('id_application', '=', $applicationId)
            ->where('is_readed', '=', '0')
            ->get();
        foreach ($readed as $read) {
            $count['application']++;
        }
        $readed = App\Models\status_chat::where('id_user', '=', $userId)
            ->where('id_application', '=', $applicationId)
            ->where('is_readed', '=', '0')
            ->get();
        foreach ($readed as $read) {
            $count['chat']++;
        }
        $readed = App\Models\status_message::where('id_user', '=', $userId)
            ->where('id_application', '=', $applicationId)
            ->where('is_readed', '=', '0')
            ->get();
        foreach ($readed as $read) {
            $count['message']++;
        }
        return $count;
    }
}
if (!function_exists('make_work')) {
    function make_work($works)
    {
        $count = count($works);
        for ($i = 0; $i < $count; $i++) {
            if ($i % 4 == 0 && !(int)$works[$i + 1]) {
                $works[$i - 4] = $works[$i - 4] . ' ' . $works[$i];
                for ($j = $i + 1; $j < $count; $j++) {
                    $works[$j - 1] = $works[$j];
                }
                unset($works[$count]);
                $count--;
                $i--;
            }
        }
        return $works;
    }
}
