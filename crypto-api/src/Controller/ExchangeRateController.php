<?php

namespace App\Controller;

use App\Entity\ExchangeRate;
use App\Service\BinanceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExchangeRateController extends AbstractController
{
    private BinanceService $binanceService;

    public function __construct(BinanceService $binanceService)
    {
        $this->binanceService = $binanceService;
    }

    #[Route('/api/rates', methods: ['GET'])]
    public function getRates(Request $request, BinanceService $binanceService, EntityManagerInterface $entityManager): JsonResponse
    {
        $currencyPair = $request->query->get('pair', 'BTC/USDT');
        $binanceSymbol = str_replace('/', '', $currencyPair);

        $price = $this->binanceService->getPrice($binanceSymbol);

        if ($price === null) {
            return $this->json(['error' => 'Failed to fetch price from Binance'], 500);
        }
        $exchangeRate = new ExchangeRate();
        $exchangeRate->setCurrencyPair($currencyPair);
        $exchangeRate->setRate($price);
        $exchangeRate->setTimestamp(new \DateTimeImmutable());
    
        $entityManager->persist($exchangeRate);
        $entityManager->flush();

        return $this->json([
            'pair' => $currencyPair,
            'price' => $price,
            'source' => 'Binance',
            'saved' => true
        ], 200, [], ['json_encode_options' => JSON_UNESCAPED_SLASHES]);
    }
    
    #[Route('/api/rates/history', methods: ['GET'])]
    public function getRatesHistory(EntityManagerInterface $entityManager): JsonResponse
    {
        $exchangeRates = $entityManager->getRepository(ExchangeRate::class)
            ->createQueryBuilder('e')
            ->orderBy('e.timestamp', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        if (empty($exchangeRates)) {
            return $this->json([
                'message' => 'No exchange rate data available. Please wait for updates.'
            ], 404);
        }

        $data = array_map(fn($rate) => [
            'pair' => $rate->getCurrencyPair(),
            'price' => $rate->getRate(),
            'timestamp' => $rate->getTimestamp()->format('Y-m-d H:i:s'),
        ], $exchangeRates);

        return $this->json($data, 200, [], ['json_encode_options' => JSON_UNESCAPED_SLASHES]);
    }
}
