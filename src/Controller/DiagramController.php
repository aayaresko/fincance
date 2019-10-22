<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Organization;
use App\Entity\Rate;
use App\Repository\CurrencyRepository;
use App\Repository\OrganizationRepository;
use App\Repository\RateRepository;
use App\Service\ChartJsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/diagram")
 */
class DiagramController extends AbstractController
{
    /**
     * @Route("/rate/{currencyId}", name="diagram_rate")
     * @Method("GET")
     */
    public function rateIndex($currencyId)
    {
        /**
         * @var ChartJsService $chartJsService
         * @var CurrencyRepository $currencyRepository
         * @var RateRepository $ratesRepository
         * @var OrganizationRepository $organizationRepository
         */
        $chartJsService         = $this->get(ChartJsService::class);
        $ratesRepository        = $this->getDoctrine()->getRepository(Rate::class);
        $organizationRepository = $this->getDoctrine()->getRepository(Organization::class);
        $currencyRepository     = $this->getDoctrine()->getRepository(Currency::class);
        $currency               = $currencyRepository->find($currencyId);
        $organizations          = $organizationRepository->findAll();
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
            'diagram/rates.html.twig',
            [
                'chartLabel' => $chartLabel,
                'labels'     => $chartJsService->prepareItems($labels),
                'data'       => json_encode($data),
                'currency'   => $currency,
            ]
        );
    }

    /**
     * @Route("/organization/{organizationId}/{currencyId}", name="diagram_organization")
     */
    public function organizationIndex($organizationId, $currencyId)
    {
        /**
         * @var ChartJsService $chartJsService
         * @var CurrencyRepository $currencyRepository
         * @var RateRepository $ratesRepository
         * @var OrganizationRepository $organizationRepository
         */
        $chartJsService         = $this->get(ChartJsService::class);
        $ratesRepository        = $this->getDoctrine()->getRepository(Rate::class);
        $organizationRepository = $this->getDoctrine()->getRepository(Organization::class);
        $currencyRepository     = $this->getDoctrine()->getRepository(Currency::class);
        $currency               = $currencyRepository->find($currencyId);
        $organization           = $organizationRepository->find($organizationId);
        if (!$currency || !$organization) {
            throw $this->createNotFoundException();
        }
        /** @var Currency $currency */
        $data       = [];
        $labels     = [];
        $chartLabel = sprintf('Chart for currency %s', $currency->getName());
        $set        = $chartJsService->buildDataSet($organization->getTitle(), []);
        $rates      = $ratesRepository
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

        return $this->render(
            'diagram/organization.html.twig',
            [
                'organization' => $organization,
                'chartLabel'   => $chartLabel,
                'labels'       => $chartJsService->prepareItems($labels),
                'data'         => json_encode($data),
                'currency'     => $currency,
            ]
        );
    }
}
