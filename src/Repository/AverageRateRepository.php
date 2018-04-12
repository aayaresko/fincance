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
        if ($value >= 1) {
            $precision = 2;
            $value     = round($value, $precision);
        } elseif ($value < 1 && $value >= 0.1) {
            $precision = 4;
            $value     = round($value, $precision);
        } elseif ($value < 0.1 && $value >= 0.01) {
            $precision = 6;
            $value     = round($value, $precision);
        } elseif ($value < 0.01 && $value >= 0.001) {
            $precision = 8;
            $value     = round($value, $precision);
        } elseif ($value < 0.001 && $value >= 0.0001) {
            $precision = 10;
            $value     = round($value, $precision);
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
