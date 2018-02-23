<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    /**
     * @var EntityManagerInterface;
     */
    private $entityManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * UserService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->encoder       = $encoder;
    }

    /**
     * @param mixed $data
     * @return User|bool
     */
    public function createActiveUser($data)
    {
        $user = $this->createUser($data);
        if ($user) {
            $user->setStatus(self::STATUS_ACTIVE);
        }

        return $user;
    }

    /**
     * @param mixed $data
     * @return User|bool
     */
    public function createInactiveUser($data)
    {
        $user = $this->createUser($data);
        if ($user) {
            $user->setStatus(self::STATUS_INACTIVE);
        }

        return $user;
    }

    /**
     * @param mixed $data
     * @param bool $doFlush
     * @return User|bool
     */
    private function createUser($data, $doFlush = false)
    {
        $entity        = new User();
        $name          = '';
        $email         = '';
        $plainPassword = '';
        $hasData       = false;
        if ($data instanceof User) {
            $hasData       = true;
            $name          = $data->getName();
            $email         = $data->getEmail();
            $plainPassword = $data->getPlainPassword();
        } elseif (is_array($data)) {
            $hasData       = true;
            $name          = $data['name'];
            $email         = $data['email'];
            $plainPassword = $data['plain_password'];
        }
        if (!$hasData) {
            return false;
        }
        $entity->setName($name);
        $entity->setEmail($email);
        $entity->setPlainPassword($plainPassword);
        $password = $this->encoder->encodePassword($entity, $plainPassword);
        $entity->setPassword($password);
        $this->entityManager->persist($entity);
        if ($doFlush) {
            $this->entityManager->flush();
        }

        return $entity;
    }
}