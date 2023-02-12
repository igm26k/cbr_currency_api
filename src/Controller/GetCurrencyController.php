<?php

namespace App\Controller;

use App\Cbr\CbrHttpClient;
use App\Cbr\DataTransformer\XmlDataTransformer;
use App\Entity\CurrencyDynamic;
use App\Form\Type\GetCurrencyType;
use App\Repository\CurrencyDynamicRepository;
use App\Service\GetCurrencyService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetCurrencyController extends AbstractApiController
{
    /**
     * @param ManagerRegistry $doctrine
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly ManagerRegistry        $doctrine,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/get-currency', name: 'get_currency', methods: [
        'GET',
        'POST'
    ])]
    public function getCurrencyAction(Request $request): Response
    {
        $form = $this->buildForm(GetCurrencyType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        [
            $currencyDynamic,
            $currencyDynamicRepository
        ] = $this->findCurrencyDynamic($request);

        if (empty($currencyDynamic)) {
            $currentDate = date('Y-m-d');
            $currencyCode = $request->get('code');
            $lastCurrency = $currencyDynamicRepository->findLastItemByCode($currencyCode);
            $getCurrencyService = new GetCurrencyService(
                $this->entityManager,
                new CbrHttpClient(),
                new XmlDataTransformer()
            );
            $dateFrom = $lastCurrency->getDate()->modify('+1 day')->format('Y-m-d');
            $getCurrencyService->getAndInsert($dateFrom, $currentDate, $currencyCode);

            [$currencyDynamic] = $this->findCurrencyDynamic($request);

            if (empty($currencyDynamic)) {
                return $this->respond('Нет актуальных данных на запрошенную дату или код валюты', Response::HTTP_NOT_FOUND);
            }
        }

        if (empty($currencyDynamic)) {
            $this->respond('Not found', Response::HTTP_NOT_FOUND);
        }

        return $this->respond([
            'value' => (float)$currencyDynamic['val'],
            'diff'  => round($currencyDynamic['diff'], 4)
        ]);
    }

    /**
     * @param Request $request
     *
     * @return array{ 0: CurrencyDynamic, 1: CurrencyDynamicRepository }
     */
    private function findCurrencyDynamic(Request $request): array
    {
        $date = $request->get('date');
        $code = $request->get('code');

        $currencyDynamicRepository = new CurrencyDynamicRepository($this->doctrine);
        $currencyDynamic = $currencyDynamicRepository->findByDateAndCode($date, $code);

        if (!$currencyDynamic || $currencyDynamic['diff'] === null) {
            return [
                null,
                $currencyDynamicRepository
            ];
        }

        return [
            $currencyDynamic,
            $currencyDynamicRepository
        ];
    }
}