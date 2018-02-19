<?php

namespace App\Repository;

use App\Entity\Rate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    /**
     * @param mixed $currency
     * @return Rate|null
     */
    public function getLowestSaleByCurrency($currency)
    {
        return $this->getOrderedByCurrencyAndAttribute($currency, 'saleValue', 'ASC');
    }

    /**
     * @param mixed $currency
     * @return Rate|null
     */
    public function getHighestBuyByCurrency($currency)
    {
        return $this->getOrderedByCurrencyAndAttribute($currency, 'buyValue', 'DESC');
    }

    /**
     * @param mixed $currency
     * @param string $attribute
     * @param string $order
     * @return mixed
     */
    private function getOrderedByCurrencyAndAttribute($currency, $attribute, $order)
    {
        return $this
            ->createQueryBuilder('r')
            ->where('r.currency = :currency')
            ->setParameter('currency', $currency)
            ->orderBy('r.' . $attribute, $order)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param mixed $currency
     * @return array
     */
    public function getAverageSaleByCurrency($currency)
    {
        return $this->getAverageByCurrency($currency, 'saleValue');
    }

    /**
     * @param mixed $currency
     * @return array
     */
    public function getAverageBuyByCurrency($currency)
    {
        return $this->getAverageByCurrency($currency, 'buyValue');
    }

    /**
     * @param mixed $currency
     * @param string $attribute
     * @return array
     */
    private function getAverageByCurrency($currency, $attribute)
    {
        return $this
            ->createQueryBuilder('r')
            ->select('AVG(r.' . $attribute . ')')
            ->where('r.currency = :currency')
            ->setParameter('currency', $currency)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('r')
            ->where('r.something = :value')->setParameter('value', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
