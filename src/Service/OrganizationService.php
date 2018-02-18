<?php

namespace App\Service;

use App\Container\Finance\OrganizationContainer;
use App\Entity\Organization;
use Doctrine\ORM\EntityManagerInterface;

class OrganizationService
{
    const TYPE_BANK = 1;
    const TYPE_EXCHANGER = 2;

    /**
     * @var EntityManagerInterface;
     */
    private $entityManager;

    /**
     * OrganizationService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $data
     * @param int $type
     * @param bool $doFlush
     * @return Organization|null
     */
    public function create($data, $type = self::TYPE_BANK, $doFlush = false)
    {
        $branch     = null;
        $title      = '';
        $address    = '';
        $url        = '';
        $externalId = '';
        $isValid    = false;
        if (is_array($data)) {
            $isValid    = true;
            $branch     = $data['branch'] ?: null;
            $title      = $data['title'];
            $address    = $data['address'];
            $url        = $data['url'];
            $externalId = $data['id'];
        } elseif ($data instanceof OrganizationContainer) {
            $isValid    = true;
            $branch     = $data->branch ?: null;
            $title      = $data->title;
            $address    = $data->address;
            $url        = $data->link;
            $externalId = $data->id;
        }
        if ($isValid) {
            $entity = new Organization();
            $entity->setType($type);
            $entity->setTitle($title);
            $entity->setUrl($url);
            $entity->setExternalIdentifier($externalId);
            $entity->setBranch($branch);
            $entity->setAddress($address);
            $this->entityManager->persist($entity);
            if ($doFlush) {
                $this->entityManager->flush();
            }

            return $entity;
        }

        return null;
    }
}