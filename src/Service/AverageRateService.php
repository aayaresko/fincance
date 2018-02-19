<?php

namespace App\Service;

use App\Entity\AverageRate;
use App\Entity\Rate;
use App\Repository\AverageRateRepository;
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
     * @var AverageRateRepository
     */
    private $averageRateRepository;

    /**
     * AverageRateService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param AverageRateRepository $averageRateRepository
     */
    public function __construct(EntityManagerInterface $entityManager, AverageRateRepository $averageRateRepository)
    {
        $this->entityManager         = $entityManager;
        $this->averageRateRepository = $averageRateRepository;
    }

    /**
     * @param Rate $rate
     * @param string $type
     * @param bool $doFlush
     * @return mixed
     */
    public function createFromRateIfNotExist(Rate $rate, $type, $doFlush = false)
    {
        $entity = new AverageRate();
        $entity->setType($type);
        $entity->setCurrency($rate->getCurrency());
        switch ($type) {
            case self::TYPE_SALE:
                $entity->setValue($rate->getSaleValue());
                break;
            case self::TYPE_BUY:
                $entity->setValue($rate->getBuyValue());
                break;
        }
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