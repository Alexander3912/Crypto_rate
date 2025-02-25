<?php

namespace App\MessageHandler;

use App\Message\UpdateExchangeRatesMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Service\BinanceService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ExchangeRate;

#[AsMessageHandler]
final class UpdateExchangeRatesMessageHandler
{
    private BinanceService $binanceService;
    private EntityManagerInterface $entityManager;

    public function __construct(BinanceService $binanceService, EntityManagerInterface $entityManager)
    {
        $this->binanceService = $binanceService;
        $this->entityManager = $entityManager;
    }

    public function __invoke(UpdateExchangeRatesMessage $message)
    {
        $currencyPairs = $message->getCurrencyPairs();

        foreach ($currencyPairs as $pair) {
            $binanceSymbol = str_replace('/', '', $pair);
            $price = $this->binanceService->getPrice($binanceSymbol);

            if ($price !== null) {
                $exchangeRate = new ExchangeRate();
                $exchangeRate->setCurrencyPair($pair);
                $exchangeRate->setRate($price);
                $exchangeRate->setTimestamp(new \DateTimeImmutable());

                $this->entityManager->persist($exchangeRate);
            }
        }
        $this->entityManager->flush();
    }
}
