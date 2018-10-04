<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function getActiveUsers($data = null)
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->where('u.status = :status')
            ->setParameter('status', UserService::STATUS_ACTIVE)
        ;
        if (is_array($data)) {
            $qb->where(
                $qb->expr()->in('u.id', $data)
            );
        }

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('u')
            ->where('u.something = :value')->setParameter('value', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
