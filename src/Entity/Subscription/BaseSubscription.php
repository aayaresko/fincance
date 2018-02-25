<?php

namespace App\Entity\Subscription;

use App\Service\SubscriptionService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class BaseSubscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id = 0;
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name = '';
    /**
     * @ORM\Column(type="integer")
     */
    protected $period = 0;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $users;

    /**
     * Subscription constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param int $period
     */
    public function setPeriod(int $period)
    {
        $periods = SubscriptionService::getAvailablePeriods();
        if (in_array($period, $periods)) {
            $this->period = $period;
        }
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param array $users
     */
    public function setUsers(array $users)
    {
        $this->users = $users;
    }
}
