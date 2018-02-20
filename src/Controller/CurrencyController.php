<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends Controller
{
    /**
     * @Route("/currency", name="currency_index")
     */
    public function index()
    {
        /**
         * @var CurrencyRepository $currencyRepository
         */
        $currencyRepository = $this->getDoctrine()->getRepository(Currency::class);
        $entities           = $currencyRepository->findAll();

        return $this->render('currency/index.html.twig', compact('entities'));
    }
}
