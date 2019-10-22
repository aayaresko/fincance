<?php

namespace App\Controller;

use App\Dto\StepsDto;
use App\Dto\TableDto;
use App\Form\StepsType;
use App\Form\TableType;
use App\Service\StepsCounter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class StatisticController extends AbstractController
{
    public function steps(Request $request, StepsCounter $service)
    {
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

    public function table(Request $request)
    {
        $dto = new TableDto();
        $form = $this->createForm(TableType::class, $dto);

        $form->add('build', SubmitType::class, ['label' => 'table.build']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this
                ->render(
                    'statistic/table/list.html.twig', ['container' => $dto]
                );
        }

        return $this
            ->render(
                'statistic/table/new.html.twig', ['form' => $form->createView()]
            );
    }
}
