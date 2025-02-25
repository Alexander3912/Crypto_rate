<?php

namespace App\Message;

class UpdateExchangeRatesMessage
{
    private array $currencyPairs;

    public function __construct(array $currencyPairs = ['BTC/USDT', 'ETH/USDT', 'BTC/EUR'])
    {
        $this->currencyPairs = $currencyPairs;
    }

    public function getCurrencyPairs(): array
    {
        return $this->currencyPairs;
    }
}
