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
     * @param Currency $currency
     * @param \DateTime $startDate
     * @return Rate|null
     */
    public function getLowestSaleByCurrency(Currency $currency, \DateTime $startDate): ?Rate
    {
        return $this->getOrderedByCurrencyAndAttribute($currency, $startDate, 'saleValue', 'ASC');
    }

    /**
     * @param Currency $currency
     * @param \DateTime $startDate
     * @return Rate|null
     */
    public function getHighestBuyByCurrency(Currency $currency, \DateTime $startDate): ?Rate
    {
        return $this->getOrderedByCurrencyAndAttribute($currency, $startDate, 'buyValue', 'DESC');
    }

    /**
     * @param Currency $currency
     * @param \DateTime $startDate
     * @param $attribute
     * @param $order
     * @return Rate|null
     */
    private function getOrderedByCurrencyAndAttribute(Currency $currency, \DateTime $startDate, $attribute, $order): ?Rate
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->where('r.currency = :currency')
            ->setParameter('currency', $currency)
            ->andWhere('r.createdAt BETWEEN :date AND :now')
            ->setParameter('date', $startDate)
            ->setParameter('now', new \DateTime())
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
     * @param \DateTime $startDate
     * @return float|null
     */
    public function getAverageSaleByCurrency(Currency $currency, \DateTime $startDate): ?float
    {
        return $this->getAverageByCurrency($currency, $startDate, 'saleValue');
    }

    /**
     * @param Currency $currency
     * @param \DateTime $startDate
     * @return float|null
     */
    public function getAverageBuyByCurrency(Currency $currency, \DateTime $startDate): ?float
    {
        return $this->getAverageByCurrency($currency, $startDate, 'buyValue');
    }

    /**
     * @param Currency $currency
     * @param \DateTime $startDate
     * @param $attribute
     * @return float|null
     */
    private function getAverageByCurrency(Currency $currency, \DateTime $startDate, $attribute): ?float
    {
        $builder = $this->createQueryBuilder('r');

        $builder
            ->select('AVG(r.' . $attribute . ')')
            ->where('r.currency = :currency')
            ->setParameter('currency', $currency)
            ->andWhere('r.createdAt BETWEEN :date AND :now')
            ->setParameter('date', $startDate)
            ->setParameter('now', new \DateTime());

        try {
            return $builder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $exception) {
            return null;
        }
    }
}
