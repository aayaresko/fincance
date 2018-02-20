<?php

namespace App\Service;

use App\Entity\User;

class UserService
{
    const TYPE_ACTIVE = 1;
    const TYPE_INACTIVE = 2;

    /**
     * @param mixed $data
     * @return User|bool
     */
    public function createActiveUser($data)
    {
        $user = $this->createUser($data);
        if ($user) {
            $user->setStatus(self::TYPE_ACTIVE);
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
            $user->setStatus(self::TYPE_INACTIVE);
        }

        return $user;
    }

    /**
     * @param mixed $data
     * @return User|bool
     */
    public function createUser($data)
    {
        $user     = new User();
        $name     = '';
        $email    = '';
        $password = '';
        $hasData = false;
        if ($data instanceof User) {
            $hasData = true;
            $name = $data->getName();
            $email = $data->getEmail();
            $password = $this->encryptPassword($data->getPassword());
        } elseif (is_array($data)) {
            $hasData = true;
            $name = $data['name'];
            $email = $data['email'];
            $password = $this->encryptPassword($data['password']);
        }
        if (!$hasData) {
            return false;
        }
        $user->setName($name);
        $user->setEmail($email);
        $user->setPassword($password);

        return $user;
    }

    /**
     * @param string $password
     * @return mixed
     */
    public function encryptPassword($password)
    {
        return $password;
    }
}