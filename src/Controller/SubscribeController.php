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
     * @Route("/subscribe/new", name="subscribe_new")
     * @Method({"GET", "POST"})
     */
    public function new(Request $request)
    {
        /**
         * @var EntityManager $em
         * @var UserService $userService
         */
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
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('success', 'User created successfully!');
                } else {
                    $this->addFlash('error', 'Something went wrong!');
                }

                return $this->redirectToRoute('subscribe_new');
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
