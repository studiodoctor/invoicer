<?php

namespace App\Helpers;

class CurrencyHelper
{
    protected static array $currencies = [
        'USD' => ['symbol' => '$', 'name' => 'US Dollar', 'position' => 'before'],
        'EUR' => ['symbol' => '€', 'name' => 'Euro', 'position' => 'before'],
        'GBP' => ['symbol' => '£', 'name' => 'British Pound', 'position' => 'before'],
        'CAD' => ['symbol' => '$', 'name' => 'Canadian Dollar', 'position' => 'before'],
        'AUD' => ['symbol' => '$', 'name' => 'Australian Dollar', 'position' => 'before'],
        'ZAR' => ['symbol' => 'R', 'name' => 'South African Rand', 'position' => 'before'],
    ];

    public static function format(float $amount, string $currency = 'ZAR'): string
    {
        $config = self::$currencies[$currency] ?? self::$currencies['ZAR'];
        $formatted = number_format($amount, 2);
        
        if ($config['position'] === 'before') {
            return $config['symbol'] . $formatted;
        }
        
        return $formatted . ' ' . $config['symbol'];
    }

    public static function symbol(string $currency = 'ZAR'): string
    {
        return self::$currencies[$currency]['symbol'] ?? 'R';
    }

    public static function all(): array
    {
        return self::$currencies;
    }

    public static function selectOptions(): array
    {
        $options = [];
        foreach (self::$currencies as $code => $config) {
            $options[$code] = "{$code} - {$config['name']}";
        }
        return $options;
    }
}