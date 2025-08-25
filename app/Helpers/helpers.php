<?php

if (! function_exists('get_extension_image_path')) {
    function get_extension_image_path(string $extension) {
        switch ($extension) {
            case 'doc':
            case 'docx':
                return '/images/extensions/doc.png';
            case 'xls':
            case 'xlsx':
                return '/images/extensions/xls.png';
            case 'jpg':
            case 'jpeg':
                return '/images/extensions/jpg.png';
            case 'png':
                return '/images/extensions/png.png';
            case 'pdf':
                return '/images/extensions/pdf.png';
            default:
        }

        return '';
    }
}

if (! function_exists('is_valid_amount_in_range')) {
    function is_valid_amount_in_range($value, $min = -1, $max = 1) {
        return (float) $value > $max || (float) $value < $min;
    }
}

if (! function_exists('get_date_and_month_from_string')) {
    function get_date_and_month_from_string($value, $reverse = false) {
        $year = substr($value, strpos($value, ' ') + 1);

        $month = '';
        if (str_contains($value, 'Январь')) {
            $month = '01';
        } elseif (str_contains($value, 'Февраль')) {
            $month = '02';
        } elseif (str_contains($value, 'Март')) {
            $month = '03';
        } elseif (str_contains($value, 'Апрель')) {
            $month = '04';
        } elseif (str_contains($value, 'Май')) {
            $month = '05';
        } elseif (str_contains($value, 'Июнь')) {
            $month = '06';
        } elseif (str_contains($value, 'Июль')) {
            $month = '07';
        } elseif (str_contains($value, 'Август')) {
            $month = '08';
        } elseif (str_contains($value, 'Сентябрь')) {
            $month = '09';
        } elseif (str_contains($value, 'Октябрь')) {
            $month = '10';
        } elseif (str_contains($value, 'Ноябрь')) {
            $month = '11';
        } elseif (str_contains($value, 'Декабрь')) {
            $month = '12';
        }

        if ($reverse) {
            return $year . '-' . $month;
        }

        return $month . '-' . $year;
    }
}

if (! function_exists('translate_month')) {
    function translate_month($month) {
        return [
            "January" => 'Январь',
            "February" => 'Февраль',
            "March" => 'Март',
            "April" => 'Апрель',
            "May" => 'Май',
            "June" => 'Июнь',
            "July" => 'Июль',
            "August" => 'Август',
            "September" => 'Сентябрь',
            "October" => 'Октябрь',
            "November" => 'Ноябрь',
            "December" => 'Декабрь',
        ][$month];
    }
}

if (! function_exists('translate_year_month_word')) {
    function translate_year_month_word($month) {
        $period = str_replace('January', 'Январь', $month);
        $period = str_replace('February', 'Февраль', $period);
        $period = str_replace('March', 'Март', $period);
        $period = str_replace('April', 'Апрель', $period);
        $period = str_replace('May', 'Май', $period);
        $period = str_replace('June', 'Июнь', $period);
        $period = str_replace('July', 'Июль', $period);
        $period = str_replace('August', 'Август', $period);
        $period = str_replace('September', 'Сентябрь', $period);
        $period = str_replace('October', 'Октябрь', $period);
        $period = str_replace('November', 'Ноябрь', $period);
        $period = str_replace('December', 'Декабрь', $period);

        return $period;
    }
}

if (! function_exists('translate_year_month')) {
    function translate_year_month($date) {
        if (is_null($date) || empty($date)) {
            return '';
        }

        $year = substr($date, 0, 4);
        $month = substr($date, 5);

        return [
            "01" => 'Январь',
            "02" => 'Февраль',
            "03" => 'Март',
            "04" => 'Апрель',
            "05" => 'Май',
            "06" => 'Июнь',
            "07" => 'Июль',
            "08" => 'Август',
            "09" => 'Сентябрь',
            "10" => 'Октябрь',
            "11" => 'Ноябрь',
            "12" => 'Декабрь',
        ][$month] . ' ' . $year;
    }
}