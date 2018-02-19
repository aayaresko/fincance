<?php

namespace App\Repository;

use App\Entity\AverageRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AverageRateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AverageRate::class);
    }

    public function findDuplicated(AverageRate $rate)
    {
        //TODO need to search by createdAt as well

        return $this->findOneBy(
            [
                'value'     => $rate->getValue(),
                'currency'  => $rate->getCurrency(),
                //'createdAt' => ''
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
