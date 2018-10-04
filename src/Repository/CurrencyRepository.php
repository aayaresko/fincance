<?php

namespace App\Repository;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    /**
     * @param string $indexBy
     * @return mixed
     */
    public function findAllIndexedBy($indexBy = 'id')
    {
        return $this->createQueryBuilder('currency')
            ->select('c')
            ->from(Currency::class, 'c', 'c.' . $indexBy)
            ->getQuery()
            ->execute();
    }

    public function findByIdentifiers(array $identifiers)
    {
        $qb = $this->createQueryBuilder('currency');

        return $qb
            ->where(
                $qb->expr()->in('currency.id', $identifiers)
            )
            ->getQuery()
            ->execute();
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('c')
            ->where('c.something = :value')->setParameter('value', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
