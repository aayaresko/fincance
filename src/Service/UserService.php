<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * UserService constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
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
     * @return User|bool
     */
    public function createUser($data)
    {
        $user          = new User();
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
        $user->setName($name);
        $user->setEmail($email);
        $user->setPlainPassword($plainPassword);
        $password = $this->encoder->encodePassword($user, $plainPassword);
        $user->setPassword($password);

        return $user;
    }
}