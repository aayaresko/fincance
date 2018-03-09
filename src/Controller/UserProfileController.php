<?php

namespace App\Controller;

use App\Form\UserProfileType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class UserProfileController extends Controller
{
    /**
     * @Route("/user/profile", name="user_profile")
     */
    public function edit(Request $request)
    {
        $em   = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $form = $this
            ->createForm(
                UserProfileType::class,
                $user,
                [
                    'selected_subscriptions' => $user->getCurrencySubscriptions()
                ]
            );
        $form->add('submit', SubmitType::class, ['label' => 'Update']);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Your profile updated successfully!');

                return $this->redirectToRoute('user_profile');
            }
        }

        return $this->render(
            'user/profile/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView()
            ]
        );
    }
}
