<?php

namespace App\Repository;

use App\Entity\Rate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Rate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rate[]    findAll()
 * @method Rate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    /**
     * @param $currency
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getLowestSaleByCurrency($currency, \DateTime $endDate)
    {
        return $this->getOrderedByCurrencyAndAttribute($currency, $endDate,'saleValue', 'ASC');
    }

    /**
     * @param $currency
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getHighestBuyByCurrency($currency, \DateTime $endDate)
    {
        return $this->getOrderedByCurrencyAndAttribute($currency, $endDate,'buyValue', 'DESC');
    }

    /**
     * @param $currency
     * @param \DateTime $endDate
     * @param $attribute
     * @param $order
     * @return mixed
     */
    private function getOrderedByCurrencyAndAttribute($currency, \DateTime $endDate, $attribute, $order)
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->where('r.currency = :currency')
            ->setParameter('currency', $currency)
            ->andWhere('r.createdAt <= :date')
            ->setParameter('date', $endDate)
            ->orderBy('r.' . $attribute, $order)
            ->setMaxResults(1);

        try {
            return $builder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $exception) {
            return null;
        }
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
