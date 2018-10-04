<?php

namespace App\Repository;

use App\Entity\Currency;
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
     * @return Rate|null
     */
    public function getLowestSaleByCurrency($currency, \DateTime $endDate): ?Rate
    {
        return $this->getOrderedByCurrencyAndAttribute($currency, $endDate, 'saleValue', 'ASC');
    }

    /**
     * @param $currency
     * @param \DateTime $endDate
     * @return Rate|null
     */
    public function getHighestBuyByCurrency($currency, \DateTime $endDate): ?Rate
    {
        return $this->getOrderedByCurrencyAndAttribute($currency, $endDate, 'buyValue', 'DESC');
    }

    /**
     * @param Currency $currency
     * @param \DateTime $endDate
     * @param $attribute
     * @param $order
     * @return Rate|null
     */
    private function getOrderedByCurrencyAndAttribute(Currency $currency, \DateTime $endDate, $attribute, $order): ?Rate
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
     * @param Currency $currency
     * @param \DateTime $endDate
     * @return float|null
     */
    public function getAverageSaleByCurrency(Currency $currency, \DateTime $endDate): ?float
    {
        return $this->getAverageByCurrency($currency, $endDate, 'saleValue');
    }

    /**
     * @param Currency $currency
     * @param \DateTime $endDate
     * @return float|null
     */
    public function getAverageBuyByCurrency(Currency $currency, \DateTime $endDate): ?float
    {
        return $this->getAverageByCurrency($currency, $endDate, 'buyValue');
    }

    /**
     * @param Currency $currency
     * @param \DateTime $endDate
     * @param $attribute
     * @return float|null
     */
    private function getAverageByCurrency(Currency $currency, \DateTime $endDate, $attribute): ?float
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('AVG(r.' . $attribute . ')')
            ->where('r.currency = :currency')
            ->setParameter('currency', $currency)
            ->andWhere('r.createdAt <= :date')
            ->setParameter('date', $endDate);

        try {
            return $builder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {
            return null;
        }
    }
}
