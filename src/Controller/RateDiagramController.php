<?php

namespace App\Controller;

use App\Entity\Rate;
use App\Repository\RateRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RateDiagramController extends Controller
{
    /**
     * @Route("/rate/diagram", name="rate_diagram")
     * @Method("GET")
     */
    public function index()
    {
        /**
         * @var RateRepository $ratesRepository
         */
        $ratesRepository = $this->getDoctrine()->getRepository(Rate::class);
        $rates           = $ratesRepository->findAll();

        return $this->render('rates/diagram/index.html.twig', compact('rates'));
    }
}
