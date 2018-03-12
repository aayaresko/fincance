<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class SubscriptionService
{
    const PERIOD_HOURLY = 3600; // 1 hour
    const PERIOD_DAILY = 86400; // 24 hours
    const PERIOD_WEEKLY = 604800; // 7 days
    const PERIOD_MONTHLY = 12096000; // 20 days

    /**
     * @var EntityManagerInterface;
     */
    private $entityManager;

    /**
     * SubscriptionService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public static function getAvailablePeriods()
    {
        return [
            'PERIOD_HOURLY'  => self::PERIOD_HOURLY,
            'PERIOD_DAILY'   => self::PERIOD_DAILY,
            'PERIOD_WEEKLY'  => self::PERIOD_WEEKLY,
            'PERIOD_MONTHLY' => self::PERIOD_MONTHLY,
        ];
    }
}