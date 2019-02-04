<?php

namespace App\Service;

use App\Dto\StepsDto;

class StepsCounter
{
    /**
     * @param StepsDto $dto
     * @return array
     */
    public function buildSteps(StepsDto $dto)
    {
        $steps = [];
        $firstStep = $dto->depositAmount * $dto->firstStepPercent / 100;
        $steps[0] = $firstStep;

        for ($i = 1; $i < $dto->number; $i++) {
            $current = $steps[$i - 1];
            $steps[$i] = $current * 2;
        }

        return $steps;
    }

    /**
     * @param StepsDto $dto
     * @return float
     */
    public function getTotalSpend(StepsDto $dto)
    {
        $steps = $this->buildSteps($dto);

        return array_sum($steps);
    }
}