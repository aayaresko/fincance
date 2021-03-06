<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Organization;
use App\Entity\Rate;
use App\Repository\CurrencyRepository;
use App\Repository\OrganizationRepository;
use App\Repository\RateRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RateController extends AbstractController
{
    /**
     * @Route("/rate/{currencyId}/{organizationId}", name="rate_index", defaults={"organizationId"=null})
     * @Method("GET")
     */
    public function index($currencyId, $organizationId)
    {
        /**
         * @var RateRepository $ratesRepository
         * @var CurrencyRepository $currencyRepository
         * @var OrganizationRepository $organizationRepository
         */
        $ratesRepository        = $this->getDoctrine()->getRepository(Rate::class);
        $currencyRepository     = $this->getDoctrine()->getRepository(Currency::class);
        $organizationRepository = $this->getDoctrine()->getRepository(Organization::class);
        $currency               = $currencyRepository->find($currencyId);
        if (!$currency) {
            throw $this->createNotFoundException();
        }
        if ($organizationId) {
            $organization = $organizationRepository->find($organizationId);
            if (!$organization) {
                throw $this->createNotFoundException();
            }
            $entities = $ratesRepository->findBy([
                'currency'     => $currency,
                'organization' => $organization
            ]);
        } else {
            $entities = $ratesRepository->findByCurrency($currencyId);
        }

        return $this->render('rate/index.html.twig', compact('entities', 'currency'));
    }
}
