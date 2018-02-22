<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Organization;
use App\Entity\Rate;
use App\Repository\CurrencyRepository;
use App\Repository\OrganizationRepository;
use App\Repository\RateRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class OrganizationController extends Controller
{
    /**
     * @Route("/organization/currencies/{organizationId}", name="organization_currency")
     */
    public function currencies($organizationId)
    {
        /**
         * @var CurrencyRepository $currencyRepository
         * @var OrganizationRepository $organizationRepository
         * @var RateRepository $rateRepository
         */
        $currencyRepository     = $this->getDoctrine()->getRepository(Currency::class);
        $organizationRepository = $this->getDoctrine()->getRepository(Organization::class);
        $rateRepository         = $this->getDoctrine()->getRepository(Rate::class);
        $organization           = $organizationRepository->find($organizationId);
        if (!$organization) {
            throw $this->createNotFoundException();
        }
        $rates       = $rateRepository->findByOrganization($organization);
        $identifiers = array_map(
            function (Rate $rate) {
                return $rate->getCurrency()->getId();
            },
            $rates
        );
        $entities    = $currencyRepository->findByIdentifiers($identifiers);

        return $this->render('organization/currencies.html.twig', compact('entities', 'organization'));
    }
}
