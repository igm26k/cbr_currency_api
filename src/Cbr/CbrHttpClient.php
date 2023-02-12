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
    // https://cbr.ru/scripts/XML_val.asp?d=0 - коды валют, устанавливаемые ежедневно (обновлять каждый день в 00:00:00)
    // https://cbr.ru/scripts/XML_val.asp?d=1 - коды валют, устанавливаемые ежемесячно (обновлять каждый месяц первого числа в 00:00:00)

    // 01.07.1992 - дата первой записи котировок в cbr.ru
    // https://cbr.ru/scripts/XML_dynamic.asp?date_req1=04/02/1988&date_req2=12/02/2023&VAL_NM_RQ=R01235
    //          получение динамики котировок между датами

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