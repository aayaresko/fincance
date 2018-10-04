<?php

namespace App\Service;

use App\Entity\AverageRate;
use App\Entity\Currency;
use App\Repository\AverageRateRepository;
use App\Repository\RateRepository;
use Doctrine\ORM\EntityManagerInterface;

class AverageRateService
{
    const TYPE_SALE = 1;
    const TYPE_BUY  = 2;

    /**
     * @var EntityManagerInterface;
     */
    private $entityManager;

    /**
     * @var RateRepository
     */
    private $rateRepository;

    /**
     * @var AverageRateRepository
     */
    private $averageRateRepository;

    /**
     * AverageRateService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RateRepository $rateRepository
     * @param AverageRateRepository $averageRateRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RateRepository $rateRepository,
        AverageRateRepository $averageRateRepository
    ) {
        $this->entityManager         = $entityManager;
        $this->rateRepository        = $rateRepository;
        $this->averageRateRepository = $averageRateRepository;
    }

    /**
     * @param Currency $currency
     * @param \DateTime $endDate
     * @param $type
     * @param bool $doFlush
     * @return AverageRate|null
     */
    public function createFromRateIfNotExist(
        Currency $currency,
        \DateTime $endDate,
        string $type,
        bool $doFlush = false
    ): ?AverageRate {
        $entity = new AverageRate();

        $entity
            ->setType($type)
            ->setCurrency($currency);

        switch ($type) {
            case self::TYPE_SALE:
                $value = $this->rateRepository->getAverageSaleByCurrency($currency, $endDate);
                break;
            case self::TYPE_BUY:
                $value = $this->rateRepository->getAverageBuyByCurrency($currency, $endDate);
                break;
            default:
                return null;
        }

        $entity->setValue($value);

        $duplicated = $this->averageRateRepository->findDuplicated($entity);

        if ($duplicated) {
            return null;
        }

        $this->entityManager->persist($entity);

        if ($doFlush) {
            $this->entityManager->flush();
        }

        return $entity;
    }
}
