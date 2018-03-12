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
        return $this->findOneBy(
            [
                'value'     => $rate->getValue(),
                'type'      => $rate->getType(),
                'currency'  => $rate->getCurrency(),
            ]
        );
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
