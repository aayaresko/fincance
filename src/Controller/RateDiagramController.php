<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Organization;
use App\Entity\Rate;
use App\Repository\CurrencyRepository;
use App\Repository\OrganizationRepository;
use App\Repository\RateRepository;
use App\Service\ChartJsService;
use App\Service\OrganizationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RateDiagramController extends Controller
{
    /**
     * @Route("/rate/diagram/{currencyId}/{organizationType}", name="rate_diagram")
     * @Method("GET")
     */
    public function index($currencyId, $organizationType = OrganizationService::TYPE_BANK)
    {
        /**
         * @var ChartJsService $chartJsService
         * @var CurrencyRepository $currencyRepository
         * @var RateRepository $ratesRepository
         * @var OrganizationRepository $organizationRepository
         */
        $chartJsService         = $this->get(ChartJsService::class);
        $currencyRepository     = $this->getDoctrine()->getRepository(Currency::class);
        $ratesRepository        = $this->getDoctrine()->getRepository(Rate::class);
        $organizationRepository = $this->getDoctrine()->getRepository(Organization::class);
        $currency               = $currencyRepository->find($currencyId);
        $organizations          = $organizationRepository->findByType($organizationType);
        if (!$currency) {
            throw $this->createNotFoundException();
        }
        /** @var Currency $currency */
        $data       = [];
        $labels     = [];
        $chartLabel = sprintf('Chart for currency %s', $currency->getName());
        foreach ($organizations as $organization) {
            /** @var Organization $organization */
            $set   = $chartJsService->buildDataSet($organization->getTitle(), []);
            $rates = $ratesRepository
                ->findBy(
                    [
                        'organization' => $organization,
                        'currency'     => $currency,
                    ],
                    [
                        'createdAt' => 'ASC'
                    ]
                );
            foreach ($rates as $rate) {
                /** @var Rate $rate */
                $labels[]               = $rate->getCreatedAt()->format('Y-m-d');
                $set['data'][]          = $rate->getSaleValue();
                $set['backgroundColor'] = $chartJsService->getRandomColor();
                $set['borderColor']     = $chartJsService->getRandomColor();
            }
            $data[] = $set;
        }

        return $this->render(
            'rate/diagram/index.html.twig',
            [
                'chartLabel' => $chartLabel,
                'labels'     => $chartJsService->prepareItems($labels),
                'data'       => json_encode($data),
                'currency'   => $currency,
            ]
        );
    }
}
