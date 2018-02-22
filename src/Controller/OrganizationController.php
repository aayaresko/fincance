<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Organization;
use App\Repository\CurrencyRepository;
use App\Repository\OrganizationRepository;
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
         */
        $currencyRepository     = $this->getDoctrine()->getRepository(Currency::class);
        $organizationRepository = $this->getDoctrine()->getRepository(Organization::class);
        $organization           = $organizationRepository->find($organizationId);
        if (!$organization) {
            throw $this->createNotFoundException();
        }
        $currencies = $currencyRepository->findAll();
        $entities   = [];
        foreach ($currencies as $index => $currency) {
            /** @var Currency $currency */
            if ($currency->getRates()->count()) {
                $entities[$index] = $currency;
            }
        }

        return $this->render('organization/currencies.html.twig', compact('entities', 'organization'));
    }
}
