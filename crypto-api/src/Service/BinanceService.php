<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinanceService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getPrice(string $symbol): ?float
    {
        $url = "https://api.binance.com/api/v3/ticker/price?symbol=" . strtoupper($symbol);

        $response = $this->httpClient->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $data = $response->toArray();

        return isset($data['price']) ? (float) $data['price'] : null;
    }
}
