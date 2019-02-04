<?php

namespace App\Controller;

use App\Dto\StepsDto;
use App\Form\StepsType;
use App\Service\StepsCounter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class StatisticController extends Controller
{
    public function steps(Request $request)
    {
        /** @var StepsCounter $service */
        $service = $this->get(StepsCounter::class);
        $dto = new StepsDto();
        $form = $this->createForm(StepsType::class, $dto);

        $form->add('calculate', SubmitType::class, ['label' => 'steps.calculate']);
        $form->handleRequest($request);

        $form->isSubmitted() && $form->isValid();

        $service->buildSteps($dto);

        return $this->render(
            'statistic/steps/new.html.twig',
            [
                'form' => $form->createView(),
                'steps' => $service->buildSteps($dto),
                'depositAmount' => $dto->depositAmount,
                'totalSpend' => $service->getTotalSpend($dto)
            ]
        );
    }
}
