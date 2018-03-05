<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SubscribeType;
use App\Service\UserService;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class SubscribeController extends Controller
{
    /**
     * @Route("/subscribe", name="subscribe")
     * @Method({"GET", "POST"})
     */
    public function new(Request $request)
    {
        /**
         * @var EntityManager $em
         * @var UserService $userService
         */
        if ($this->getUser()) {
            $this->addFlash('notice', 'You are already subscribed.');

            return $this->redirectToRoute('user_profile');
        }
        $em          = $this->get('doctrine.orm.entity_manager');
        $userService = $this->get(UserService::class);
        $user        = new User();
        $form        = $this->createForm(SubscribeType::class, $user);
        $form->add('submit', SubmitType::class, ['label' => 'Subscribe']);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /** @var User $data */
                $data = $form->getData();
                $user = $userService->createActiveUser($data);
                if ($user) {
                    $em->flush();
                    $this->addFlash('success', 'User created successfully!');
                } else {
                    $this->addFlash('danger', 'Something went wrong!');
                }

                return $this->redirectToRoute('subscribe');
            }
        }

        return $this->render(
            'subscribe/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
