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
        $builder   = $this->createQueryBuilder('r');
        $value     = $rate->getValue();
        $precision = 0;

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
            $builder->select('r', 'ROUND(r.value, ' . $precision . ') as rounded_value');
            $builder->having('rounded_value = :value');
        } else {
            $builder->where('r.value = :value');
        }

        $builder
            ->andWhere('r.type = :type')
            ->andWhere('r.currency = :currency')
            ->setParameter('type', $rate->getType())
            ->setParameter('currency', $rate->getCurrency())
            ->setParameter('value', $value)
            ->setMaxResults(1);

        return $builder->getQuery()->execute();
    }
}
