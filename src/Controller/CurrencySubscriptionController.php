<?php

namespace App\Controller;

use App\Entity\Subscription\CurrencySubscription;
use App\Form\CurrencySubscriptionType;
use App\Service\SubscriptionService;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/subscription")
 */
class CurrencySubscriptionController extends Controller
{
    /**
     * @Route("/currency", name="subscription_currency")
     */
    public function new(Request $request)
    {
        /**
         * @var EntityManager $em
         * @var SubscriptionService $subscriptionService
         */
        $em                  = $this->get('doctrine.orm.entity_manager');
        $subscriptionService = $this->get(SubscriptionService::class);
        $subscription        = new CurrencySubscription();
        $form                = $this->createForm(CurrencySubscriptionType::class, $subscription);
        $form->add('submit', SubmitType::class, ['label' => 'Create']);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /** @var CurrencySubscription $data */
                $data         = $form->getData();
                $subscription = $subscriptionService->createCurrencySubscription($data);
                if ($subscription) {
                    $em->flush();
                    $this->addFlash('success', 'Currency subscription created successfully!');
                } else {
                    $this->addFlash('error', 'Something went wrong!');
                }

                return $this->redirectToRoute('subscription_currency');
            }
        }

        return $this->render(
            'subscription/currency/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
