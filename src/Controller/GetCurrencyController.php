<?php

namespace App\Controller;

use App\Entity\CurrencyDynamic;
use App\Form\Type\GetCurrencyType;
use App\Repository\CurrencyDynamicRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GetCurrencyController extends AbstractApiController
{
    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    #[Route('/get-currency', name: 'get_currency', methods: [
        'GET',
        'POST'
    ])]
    public function updateAction(Request $request): Response
    {
        $form = $this->buildForm(GetCurrencyType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        [$currencyDynamic] = $this->findCurrencyDynamic($request);

        if (empty($currencyDynamic)) {
            $this->respond('Not found', Response::HTTP_NOT_FOUND);
        }

        return $this->respond([
            'value' => (float)$currencyDynamic[0]['val'],
            'diff'  => round($currencyDynamic[0]['diff'], 4)
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

        if (!$currencyDynamic) {
            throw new NotFoundHttpException('Not found');
        }

        return [
            $currencyDynamic,
            $currencyDynamicRepository
        ];
    }
}