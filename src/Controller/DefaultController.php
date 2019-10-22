<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Organization;
use App\Repository\CurrencyRepository;
use App\Repository\OrganizationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Method("GET")
     */
    public function index()
    {
        /**
         * @var CurrencyRepository $currencyRepository
         * @var OrganizationRepository $organizationRepository
         */
        $currencyRepository = $this->getDoctrine()->getRepository(Currency::class);
        $organizationRepository = $this->getDoctrine()->getRepository(Organization::class);

        return $this->render('index.html.twig', [
            'currencies' => $currencyRepository->findAll(),
            'organizations' => $organizationRepository->findAll(),
        ]);
    }
}
