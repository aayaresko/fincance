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
    const TYPE_BUY = 2;

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
     * @param string $type
     * @param bool $doFlush
     * @return mixed
     */
    public function createFromRateIfNotExist(Currency $currency, $type, $doFlush = false)
    {
        $entity = new AverageRate();
        $entity->setType($type);
        $entity->setCurrency($currency);
        switch ($type) {
            case self::TYPE_SALE:
                $value = $this->rateRepository->getAverageSaleByCurrency($currency);
                break;
            case self::TYPE_BUY:
                $value = $this->rateRepository->getAverageBuyByCurrency($currency);
                break;
            default:
                $value = null;
        }
        if (!$value) {
            return false;
        }
        $entity->setValue($value);
        $duplicated = $this->averageRateRepository->findDuplicated($entity);
        if ($duplicated) {
            return false;
        }
        $this->entityManager->persist($entity);
        if ($doFlush) {
            $this->entityManager->flush();
        }

        return $entity;
    }
}
