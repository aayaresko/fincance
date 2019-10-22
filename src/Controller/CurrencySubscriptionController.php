<?php

namespace App\Controller;

use App\Entity\Subscription\CurrencySubscription;
use App\Form\CurrencySubscriptionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/subscription")
 */
class CurrencySubscriptionController extends AbstractController
{
    /**
     * @Route("/currency", name="subscription_currency")
     */
    public function new(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em     = $this->getDoctrine()->getManager();
        $entity = new CurrencySubscription();
        $form   = $this->createForm(CurrencySubscriptionType::class, $entity);
        $form->add('submit', SubmitType::class, ['label' => 'Create']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'Currency subscription created successfully!');

            return $this->redirectToRoute('subscription_currency');
        }

        return $this->render(
            'subscription/currency/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
