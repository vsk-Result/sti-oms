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
    function get_date_and_month_from_string($value) {
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

        return $month . '-' . $year;
    }
}