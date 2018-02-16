<?php

namespace App\Controller;

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
        return $this->render('rates/diagram/index.html.twig');
    }
}
