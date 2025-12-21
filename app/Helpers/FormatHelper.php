<?php

if (!function_exists('format_price')) {
    /**
     * Format price to Vietnamese currency format
     *
     * @param float|int|null $price
     * @param string $currency
     * @param int $decimals
     * @return string
     */
    function format_price($price, $currency = '₫', $decimals = 0): string
    {
        if ($price === null || $price === '') {
            return $currency . '0';
        }

        $formatted = number_format((float) $price, $decimals, ',', '.');
        return $currency . $formatted;
    }
}

if (!function_exists('format_number')) {
    /**
     * Format number with Vietnamese format
     *
     * @param float|int|null $number
     * @param int $decimals
     * @return string
     */
    function format_number($number, $decimals = 0): string
    {
        if ($number === null || $number === '') {
            return '0';
        }

        return number_format((float) $number, $decimals, ',', '.');
    }
}
