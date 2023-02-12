<?php

namespace App\Command;

use App\Cbr\CbrHttpClient;
use App\Cbr\DataTransformer\XmlDataTransformer;
use App\Entity\CurrencyCode;
use App\Service\GetCurrencyService;
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

        $currentDate = date('Y-m-d');
        $client = new CbrHttpClient();
        $transformer = new XmlDataTransformer();

        $io->info('Get currencies from cbr.ru');

        $currencies = $this->entityManager->getRepository(CurrencyCode::class)->findAll();

        foreach ($currencies as $currency) {
            $currencyCode = $currency->getID();

            $getCurrencyService = new GetCurrencyService(
                $this->entityManager,
                $client,
                $transformer,
                $io
            );
            $getCurrencyService->getAndInsert('1950-01-01', $currentDate, $currencyCode);
        }

        $io->success('Successfully inserted');

        return Command::SUCCESS;
    }
}
