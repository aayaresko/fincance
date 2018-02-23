<?php

namespace App\Controller;

use App\Entity\Subscription\CurrencySubscription;
use App\Entity\User;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
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
        /**
         * @var EntityManager $em
         * @var UserRepository $userRepository
         */
        $em             = $this->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);

        $user = $userRepository->findOneByEmail('aayaresko@gmail.com');
        $form = $this
            ->createForm(
                UserProfileType::class,
                $user,
                ['selected_subscriptions' => $user->getCurrencySubscriptions()]
            );
        $form->add('submit', SubmitType::class, ['label' => 'Update']);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->persist($user);
                $em->flush();

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
