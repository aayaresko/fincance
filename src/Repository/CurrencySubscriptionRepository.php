<?php

namespace App\Repository;

use App\Entity\Subscription\CurrencySubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CurrencySubscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencySubscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencySubscription[]    findAll()
 * @method CurrencySubscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencySubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CurrencySubscription::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('s')
            ->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
