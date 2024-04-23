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