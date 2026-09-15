<?php

use Illuminate\Support\Facades\Session;

if (!function_exists('formatPrice')) {
    /**
     * Format a given amount in GHS to the user's local currency.
     *
     * @param float|int $amountInGHS The amount in GHS.
     * @return string The formatted price.
     */
    function formatPrice($amountInGHS)
    {
        $currency = Session::get('user_currency', 'GHS');
        $symbol = Session::get('user_currency_symbol', 'GH₵');
        $exchangeRate = Session::get('user_exchange_rate', 1.0);

        // Calculate converted amount
        $convertedAmount = (float) $amountInGHS * $exchangeRate;

        // Format the converted amount (e.g., $15.00, GH₵25.50)
        // Some currencies don't use decimals, but standard formatting is usually 2 decimals.
        return $symbol . number_format($convertedAmount, 2);
    }
}
