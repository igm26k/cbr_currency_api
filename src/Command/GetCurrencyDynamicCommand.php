<?php

namespace App\Command;

use App\Cbr\CbrHttpClient;
use App\Cbr\DataTransformer\XmlDataTransformer;
use App\Entity\CurrencyCode;
use App\Entity\CurrencyDynamic;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'getCurrencyDynamic',
    description: 'Get currencies dynamic changes from cbr.ru and populate currency_dynamic table',
)]
class GetCurrencyDynamicCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, string $name = null)
    {
        $this->entityManager = $entityManager;

        parent::__construct($name);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '4G');

        $io = new SymfonyStyle($input, $output);

        $batchSize = 5000;
        $currentDate = date('Y-m-d');
        $client = new CbrHttpClient();
        $transformer = new XmlDataTransformer();

        $io->info('Get currencies from cbr.ru');

        $currencies = $this->entityManager->getRepository(CurrencyCode::class)->findAll();

        foreach ($currencies as $currency) {
            $currencyCode = $currency->getID();

            $currencyDynamicXml = $client->fetchCurrencyDynamic('1950-01-01', $currentDate, $currencyCode);
            $xmlObj = $transformer->transform($currencyDynamicXml);
            $records = $xmlObj->xpath('//Record');

            $io->info('Insert currencies to table "currency_dynamic" for ' . $currencyCode);

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

                if (($i % $batchSize) === 0) {
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

        $io->success('Successfully inserted');

        return Command::SUCCESS;
    }
}
