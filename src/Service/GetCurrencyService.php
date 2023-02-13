<?php

namespace App\Service;

use App\Cbr\CbrHttpClient;
use App\Cbr\DataTransformer\XmlDataTransformer;
use App\Entity\CurrencyDynamic;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GetCurrencyService
{
    private int                    $batchSize = 5000;
    private EntityManagerInterface $entityManager;
    private CbrHttpClient          $client;
    private XmlDataTransformer     $transformer;
    private ?SymfonyStyle          $io;

    /**
     * @param EntityManagerInterface $entityManager
     * @param CbrHttpClient $client
     * @param XmlDataTransformer $transformer
     * @param SymfonyStyle|null $io
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CbrHttpClient          $client,
        XmlDataTransformer     $transformer,
        ?SymfonyStyle          $io = null
    )
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->transformer = $transformer;
        $this->io = $io;
    }

    /**
     * @param $dateFrom
     * @param $dateTo
     * @param $currencyCode
     * @return void
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getAndInsert($dateFrom, $dateTo, $currencyCode): void
    {
        $currencyDynamicXml = $this->client->fetchCurrencyDynamic($dateFrom, $dateTo, $currencyCode);
        $xmlObj = $this->transformer->transform($currencyDynamicXml);
        $records = $xmlObj->xpath('//Record');

        $this->io?->info('Insert currencies to table "currency_dynamic" for ' . $currencyCode);

        $i = 1;

        foreach ($records as $record) {
            $currencyId = (string)$record->attributes()?->Id;

            $date = (string)$record->attributes()?->Date;
            $datetime = new DateTime($date);

            $value = (float)str_replace(',', '.', $record->Value);

            $currencyDynamic = new CurrencyDynamic();
            $currencyDynamic->setCurrencyID(trim($currencyId));
            $currencyDynamic->setDate($datetime);
            $currencyDynamic->setValue($value);
            $currencyDynamic->setNominal(trim($record->Nominal));

            $this->entityManager->persist($currencyDynamic);

            if (($i % $this->batchSize) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
                $i = 1;
                continue;
            }

            unset($currencyDynamic, $datetime, $record);

            $i++;
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        unset($currencyDynamic, $currencyDynamicXml, $xmlObj, $record, $records, $datetime, $currency);
    }
}