<?php

use App\Helpers\CurrencyHelper;

if (!function_exists('format_currency')) {
    function format_currency(float $amount, string $currency = 'ZAR'): string
    {
        return CurrencyHelper::format($amount, $currency);
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(string $currency = 'ZAR'): string
    {
        return CurrencyHelper::symbol($currency);
    }
}