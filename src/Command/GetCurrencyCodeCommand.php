<?php

namespace App\Command;

use App\Cbr\CbrHttpClient;
use App\Cbr\DataTransformer\XmlDataTransformer;
use App\Entity\CurrencyCode;
use App\Repository\CurrencyCodeRepository;
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
    name: 'getCurrencyCode',
    description: 'Get currencies from cbr.ru and populate currency_code table',
)]
class GetCurrencyCodeCommand extends Command
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
        $io = new SymfonyStyle($input, $output);

        $client = new CbrHttpClient();
        $transformer = new XmlDataTransformer();

        $io->info('Get currencies from cbr.ru');

        $everyDayCodesXml = $client->fetchCurrencyCodes();
        $xmlObj = $transformer->transform($everyDayCodesXml);
        $currencies = $xmlObj->xpath('//Item');

        $io->info('Delete all from table "currency_code"');

        $this->entityManager->getRepository(CurrencyCode::class)->deleteAll();

        $io->info('Insert currencies to table "currency_code"');

        foreach ($currencies as $currency) {
            $currencyId = (string)$currency->attributes()?->ID;

            $currencyCode = new CurrencyCode();
            $currencyCode->setID(trim($currencyId));
            $currencyCode->setParentCode(trim($currency->ParentCode));
            $currencyCode->setName(trim($currency->Name));
            $currencyCode->setEngName(trim($currency->EngName));
            $currencyCode->setNominal(trim($currency->Nominal));

            $this->entityManager->persist($currencyCode);
        }

        $this->entityManager->flush();

        $io->success('Successfully inserted');

        return Command::SUCCESS;
    }
}
