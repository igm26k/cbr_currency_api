<?php

namespace App\Cbr;

use App\Cbr\DataTransformer\DateTransformer;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CbrHttpClient
{
    const CURRENCY_CODES_URL   = 'https://cbr.ru/scripts/XML_val.asp';
    const CURRENCY_DYNAMIC_URL = 'https://cbr.ru/scripts/XML_dynamic.asp';

    private HttpClientInterface $client;

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    /**
     * @param int $d
     * @return string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function fetchCurrencyCodes(int $d = 0): string
    {
        $response = $this->client->request('GET', self::CURRENCY_CODES_URL, [
            'query' => ['d' => $d]
        ]);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new Exception('Can not get content from ' . self::CURRENCY_CODES_URL);
        }

        return $response->getContent();
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     * @param string $currencyCode
     * @return string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function fetchCurrencyDynamic(
        string $fromDate,
        string $toDate,
        string $currencyCode
    ): string
    {
        $dateTransformer = new DateTransformer();
        $fromDate = $dateTransformer->transform($fromDate);
        $toDate = $dateTransformer->transform($toDate);

        $response = $this->client->request('GET', self::CURRENCY_DYNAMIC_URL, [
            'query' => [
                'date_req1' => $fromDate,
                'date_req2' => $toDate,
                'VAL_NM_RQ' => $currencyCode
            ]
        ]);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new Exception('Can not get content from ' . self::CURRENCY_CODES_URL);
        }

        return $response->getContent();
    }
}