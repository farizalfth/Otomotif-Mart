<?php

use Illuminate\Support\Number;

if (!function_exists('rupiah')) {
    /**
     * Format angka ke dalam format mata uang Rupiah.
     *
     * @param int|float $number
     * @return string
     */
    function rupiah($number): string
    {
        return 'Rp' . number_format($number, 0, ',', '.');
    }
}

if (!function_exists('idr')) {
    /**
     * Alternatif format menggunakan Number::currency() Laravel 10+
     *
     * @param int|float $number
     * @return string
     */
    function idr($number): string
    {
        return Number::currency($number, 'IDR', locale: 'id');
    }
}
