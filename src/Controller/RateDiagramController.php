<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Rate;
use App\Repository\CurrencyRepository;
use App\Repository\RateRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RateDiagramController extends Controller
{
    /**
     * @Route("/rate/diagram/{currencyId}", name="rate_diagram")
     * @Method("GET")
     */
    public function index($currencyId)
    {
        /**
         * @var CurrencyRepository $currencyRepository
         * @var RateRepository $ratesRepository
         */
        $currencyRepository = $this->getDoctrine()->getRepository(Currency::class);
        $ratesRepository    = $this->getDoctrine()->getRepository(Rate::class);
        $currency           = $currencyRepository->find($currencyId);
        if (!$currency) {
            throw $this->createNotFoundException();
        }
        $rates = $ratesRepository->findByCurrency($currency);

        return $this->render('rates/diagram/index.html.twig', compact('rates', 'currency'));
    }
}
