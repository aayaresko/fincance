<?php

namespace App\Repository;

use App\Entity\AverageRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AverageRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method AverageRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method AverageRate[]    findAll()
 * @method AverageRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AverageRateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AverageRate::class);
    }

    public function findDuplicated(AverageRate $rate)
    {
        $qb        = $this->createQueryBuilder('r');
        $precision = 0;
        $value     = $rate->getValue();
        if ($rate->getValue() >= 1) {
            $precision = 2;
            $value = round($rate->getValue(), $precision);
        } elseif ($rate->getValue() < 1 && $rate->getValue() >= 0.1) {
            $precision = 5;
            $value = round($rate->getValue(), $precision);
        }
        if ($precision) {
            $qb->select('r', 'ROUND(r.value, ' . $precision . ') as rounded_value');
            $qb->having('rounded_value = :value');
        } else {
            $qb->where('r.value = :value');
        }
        $qb
            ->andWhere('r.type = :type')
            ->andWhere('r.currency = :currency')
            ->setParameter('type', $rate->getType())
            ->setParameter('currency', $rate->getCurrency())
            ->setParameter('value', $value)
            ->setMaxResults(1);

        return $qb->getQuery()->execute();
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('o')
            ->where('o.something = :value')->setParameter('value', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
